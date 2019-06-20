<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/3
 * Time: 4:04 PM
 */
namespace Tw\Server\Models;
use Tw\Server\Facades\Tw;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Activity extends Model
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
    protected $or_fields = ['title','id'];
    /**
     * @var array and 条件查询字段
     */
    protected $and_fields = ['days','level'];


    /**
     * Activity constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('tw.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable('tw_activity');

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('Tw\Server\Models\Admin');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function players()
    {
        return $this->hasMany('Tw\Server\Models\Player');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function judges()
    {
        return $this->hasMany('Tw\Server\Models\Judges');
    }

    /**
     * @return string
     */
    public function getIndexUrl(): string
    {
        return route('tw.activity.index');
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
     * @return string
     */
    public function getTermAttribute():string
    {
        if ($this->release_state) {
            if ($this->is_term)
                $sData = '<span>'.$this->released_at." 至 ".$this->term_date.'</span>';
            else
                $sData = '<span>已过期</span>';
        } else
            $sData = '<span>暂未发布</span>';

        return $sData;
    }

    public function getJumpAttribute():string
    {
        $str = '';
        if ($this->is_term) {
            $url = route('tw.home',$this->id);
            $str = "<a class=\"btn btn-primary btn-xs\" target=\"_blank\" href=\"$url\"><i class=\"fa fa-hand-pointer-o\"></i> 前往活动</a>";
        }
        return $str;
    }

    /**
     * @return string
     */
    public function getTermDateAttribute():string
    {
        return $this->getTermDate();
    }

    /**
     * @param int|null $days
     * @param string|null $time
     * @return string
     */
    public function getTermDate(int $days = null ,string $time = null):string
    {
        return date('Y-m-d H:i:s',strtotime("+".($days ?? $this->days)."day",strtotime(($time ?? $this->released_at))));
    }


    /**
     * @param string $dTerm
     * @return bool
     */
    public function IsTerm(string $dTerm = null):bool
    {
        return ($dTerm ?? $this->term_date) > date('Y-m-d H:i:s');
    }

    /**
     * @return bool
     */
    public function getIsTermAttribute():bool
    {
        return $this->IsTerm();
    }


    /**
     * @param int $activityIds
     * @return array
     * @see 获取首页有效活动
     */
    public function getHomeActivity(int $activityIds):object
    {
        $object =  (object)null;
        $oData = $this->find($activityIds);
        if ($oData) {
            $dResult = $this->getTermDate($oData['days'],$oData['released_at']);
            if ($this->IsTerm($dResult)) {
                $object = $oData;
            }
        }
       return $object;
    }

    /**
     * @param int $id
     * @see 获取下一位推送选手的id
     */
    public function getNextPushStateIdByActivityId(int $id):int
    {
        $nextplayerId  = null;
        $id = $id??null;
        $oAcitity = $this->find($id);
        if ((array)($oAcitity)) {
            $player = get_push_player($id);
            if (!$player) {
                $nextplayerId = $oAcitity->players()->orderBy('id','desc')->value('id');
            } else {
                $nextplayerId = $oAcitity->players()->where('id',"<",$player['id'])->orderBy('id','desc')->value('id');
            }
        }
        return (int)$nextplayerId ;
    }

    /**
     * @return array
     * @see 普通用户 最多添加n个评委
     */
    public function beforeUpdate(object $oData):string
    {
        $message = '';
        return $this->restrict(2) ? $message : "普通项目个数不能超过5个！";
    }

    /**
     * @param array $aData
     * @return string
     * @see 插入之前钩子
     */
    public function beforeInsert(array $aData):string
    {
        $message = '';
        if ($aData['level'] == 1)
            $message = $this->restrict(1) ? '' : "普通项目个数不能超过5个！";

        return $message;
    }

    /**
     * @see 插入之后钩子处理
     */
    public function afterInsert():void
    {
        if ($this->level == 2) {
            $this->storeHighLevel();
        }
    }

    /**
     * @see 创建高级活动处理
     */
    public function storeHighLevel():void
    {
        // 软删除自己
        $aData = $this->toArray();
        $this->delete();
        $aInput['type'] = 1;
        $aInput['level'] = 1;
        $aInput['activity_id'] = $aData['id'];
        $aInput['pay_type'] = 1;
        $mPayOrder = Tw::newModel("PayOrder");
        Tw::moldelLogic($mPayOrder)->generateOrder($aInput);
        $this->jumpUrl = $mPayOrder->getIndexUrl();
    }


    /**
     * @see 项目限制
     */
    public function restrict(int $iFlag):bool
    {
        $bFlag = true;
        $limit = config('tw.restrict.activity',5);
        $aWhere['admin_id'] = Tw::authLogic()->guard()->id();
        $aWhere['level'] = 1;
        $activitys = Tw::newModel('Activity')->where($aWhere)->count();
        if ($activitys > 0) {
            if ($iFlag == 1) {
                $bFlag = $limit > $activitys;
            } elseif ($iFlag == 2) {
                $bFlag = $limit >= $activitys;
            }
        }
        return $bFlag;
    }

    /**
     * @param array $id
     */
    public function release(array $aId):bool
    {
        $aWhere['admin_id'] = Tw::authLogic()->guard()->id();
        $aWhere['id'] = $aId[0];
        $aInput['release_state'] = 1;
        $aInput['released_at'] = date("Y-m-d H:i:s");
        return $this->where($aWhere)->where('release_state','<>',1)->update($aInput);
    }






}
