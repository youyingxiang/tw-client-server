<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/5
 * Time: 10:23 AM
 */
namespace Tw\Server\Models;
use Tw\Server\Facades\Tw;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\SoftDeletes;

class Judges extends Model
{

    /**
     * 软删除
     */
    use SoftDeletes;
    /**
     * @var array
     */
    protected $dates = ['delete_at'];
    /**
     * @var array or 条件查询字段
     */
    protected $or_fields = ['name'];
    /**
     * @var array and 条件查询字段
     */
    protected $and_fields = ['activity_id'];

    /**
     * Judges constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('tw.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('tw_judges');

        parent::__construct($attributes);
    }
    /**
     * @return string
     */
    public function getIndexUrl(): string
    {
        $url = empty(request()->input('activity_id'))
            ? route('tw.judges.index')
            : route('tw.judges.index')."?activity_id=".hash_encode(request()->input('activity_id'));
        return $url;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('Tw\Server\Models\Admin');
    }
    /**
     * @return string
     * @see 获取hashid
     */
    public function getHidAttribute():string
    {
        return hash_encode($this->id)??$this->id;
    }

    /**
     * @return array
     */
    public function getOrFields():array
    {
        return $this->or_fields??[];
    }

    /**
     * @return array
     */
    public function getAndFieds():array
    {
        return $this->and_fields??[];
    }
    /**
     * 标示 当前活动属于哪个项目
     */
    public function parentFlag():array
    {
        return ['admin_id'=>Tw::authLogic()->guard()->id()];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity()
    {
        return $this->belongsTo('Tw\Server\Models\Activity');
    }

    /**
     * @param $value
     * @return string
     * @see 获取二维码
     */
    public function getQrCodeAttribute():string
    {
        return QrCode::size(100)->generate(tw_route("tw.home.judges",$this->id));
    }

    /**
     * @return array
     * @see 普通用户 最多添加n个评委
     */
    public function restrict(string $activity_id,int $iFlag):bool
    {
        $bFlag = false;
        $activityInfo = Tw::newModel('Activity')->where('admin_id',Tw::authLogic()->guard()->id())->find($activity_id);
        $limit =  $activityInfo->release_state
            ? config('tw.restrict.judges',5)
            : config('tw.restrict.norelease_judges',2);
        if (isset($activityInfo['level']) && $activityInfo['level'] == 1) {
            $players = $this->where(['admin_id' => Tw::authLogic()->guard()->id(),'activity_id'=>$activity_id])->count();
            if ($players > 0)
                $bFlag =  ($iFlag == 1) ? $limit > $players : $limit >= $players;
            else if ($players == 0)
                $bFlag = true;
        } else if (isset($activityInfo['level']) && $activityInfo['level'] == 2)
            $bFlag = true;
        return $bFlag;
    }
    /**
     * @param array $aData
     * @return string
     * @see 插入之前的钩子
     */
    public function beforeInsert(array $aData):string
    {
        return $this->restrict($aData['activity_id'],1) ? '' : "评委超过限制！请升级高级活动";
    }

    /**
     * @param int $id
     * @see 修改之前钩子
     */
    public function beforeUpdate(object $oData):string
    {
        if ($oData->link_state == 1) {
            $this->setPushClearJudgesLinkUrl($oData);
        }
        return $this->restrict($oData->activity_id,2) ? '' : "评委超过限制！请升级高级活动";
    }

    /**
     * @see 设置清除评委后把评委页面 返回首页的websocket连接
     * 请求这个url 将进行推送
     */
    public function setPushClearJudgesLinkUrl(object $oData):void
    {
        $token   = hash_make(['clearJudgesLink',$oData->id,$oData->activity_id]);
        $this->PushClearJudgesLinkUrl = $_SERVER['HTTP_HOST'].":9502?page=clearJudgesLink&judges_id=$oData->id&activity_id=$oData->activity_id&token=".$token;
    }


    /**
     * @param object $oData
     * @修改之后的钩子
     */
    public function afterUpdate(object $oData)
    {
        if ($oData->link_state == 0 && isset($this->PushClearJudgesLinkUrl)) {
            curl_get($this->PushClearJudgesLinkUrl);
        }

    }

    /**
     * @param $id 评委id
     * @return array
     */
    public function getPlayerByRedis(int $id):array
    {
        $oJudges = $this->find($id);
        return get_push_player($oJudges['activity_id']);
    }

    /**
     * @param int $id
     * @检测评委连接状态
     */
    public function checkLinkState():bool
    {
       $bRes =  false;
       if ($this->link_state == 0) {  //还没有用户进入界面
           $this->unlinked();
           $bRes = true;
       } elseif ($this->checkLinkedIsOneSelf()) { //是当前用户登陆过在登陆
           $bRes = true;
       }
       return $bRes;
    }

    /**
     * @param object $oJudges
     * @see 未链接
     */
    public function unlinked():void
    {
        $this->link_state = 1;
        $this->session_id = session()->getId();
        $this->save();
        session(["link_state"=>1]);
        $this->pushJudgesLoginDynamic();
    }

    /**
     * @see 检测链接是当前用户链接的
     */
    public function checkLinkedIsOneSelf():bool
    {
        $bRes = false;
        if ($this->link_state == 1
            && session("link_state") == 1
            && session()->getId() == $this->session_id) {
            $bRes = true;
           $this->pushJudgesLoginDynamic();
        }
        return $bRes;
    }

    /**
     * @see 通知后台 更改评委扫码哦登陆状态
     */
    public function pushJudgesLoginDynamic()
    {
        curl_get($this->getPushJudgesLoginDynamicUrl());
    }

    /**
     * @see 获取扫码登陆状态推送url
     */
    public function getPushJudgesLoginDynamicUrl():string
    {
        $token   = hash_make(['judgesLoginDynamic',$this->link_state,$this->id,$this->activity_id]);
        $sUrl    = $_SERVER['HTTP_HOST'].":9502?page=judgesLoginDynamic&linkstate=$this->link_state&judges_id=$this->id&activity_id=$this->activity_id&token=".$token;
        return $sUrl;
    }



}