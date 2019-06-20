<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/5
 * Time: 10:23 AM
 */
namespace Tw\Server\Models;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
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
    protected $or_fields = ['name','score'];
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

        $this->setTable('tw_player');

        parent::__construct($attributes);
    }
    /**
     * @return string
     */
    public function getIndexUrl(): string
    {
        $url = empty(request()->input('activity_id'))
            ? route('tw.player.index')
            : route('tw.player.index')."?activity_id=".hash_encode(request()->input('activity_id'));
        return $url;
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
     * @see 获取hashid
     */
    public function getHidAttribute():string
    {
        return hash_encode($this->id)??$this->id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity()
    {
        return $this->belongsTo('Tw\Server\Models\Activity');
    }
    /**
     * @return array
     * @see 普通用户 最多添加n个选手
     */
    public function restrict(string $activity_id,int $iFlag):bool
    {
        $bFlag = false;
        $limit = config('tw.restrict.player',10);
        $activityInfo = Tw::newModel('Activity')->where('admin_id',Tw::authLogic()->guard()->id())->find($activity_id);
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
     * @param array $aId
     */
    public function pushPlayer(array $aId)
    {
        $bSaveRes = false;
        $id = $aId[0]??0;
        $player  =  $this->where('admin_id',Tw::authLogic()->guard()->id())->find($id);

        if ($player && $player->push_state != 1) {
           $player->push_state = 1;
           DB::transaction(function ()use($player,$id,&$bSaveRes) {
               $player->save();
               $this->where('id', '<>', $id)->where('activity_id',$player['activity_id'])->update(['push_state' => 0]);
               $this->storePushPlayer($player->toArray());
               $bSaveRes = true;
           });
        }

        if ($player->push_state == 1)
            return Tw::ajaxResponse("操作成功",$this->getIndexUrl()."?activity_id=".hash_encode($player['activity_id']));
        else
            return Tw::ajaxResponse("操作失败");


    }

    /**
     * @param array $player
     * @see 将当前活动推送上去的选手存储在redis
     */
    public function storePushPlayer(array $player):void
    {
        if (!empty($player) && is_array($player)) {
            $playerKey = config('tw.redis_key.h3');
            $field = $player['activity_id'];
            Redis::hset($playerKey,$field,json_encode($player,true));
//            Redis::del(config('tw.redis_key.h1').$player['id']);
        }
    }

    /**
     * @param int $activityId
     * @return array
     * @see 选手得分排名
     */
    public function getRank(int $activityId):array
    {
        $aData = $this->where("activity_id",$activityId)->orderBy('score','desc')->get()->toArray();
        return $aData;
    }

    /**
     * @param array $ids
     * @see 删除之前对选手的redis同步
     */
    public function beforeDelete(array $ids)
    {
        foreach ($ids as $id) {
            $activity_id = $this->where('id',$id)->value('activity_id');
            $pushPlayer = get_push_player($activity_id);
            if (!empty($pushPlayer) && $pushPlayer['id'] == $id)
                del_push_player($activity_id); // 如果推送选手在删除之内 将其删除
            $playerKey = config('tw.redis_key.h1').$id; // 删除得分信息
            Redis::del($playerKey);
        }
    }

    /**
     * @param array $aData
     * @return string
     * @see 插入之前的钩子
     */
    public function beforeInsert(array $aData):string
    {
        return $this->restrict($aData['activity_id'],1) ? '' : "选手超过限制！请升级高级活动";
    }

    /**
     * @param int $id
     * @see 修改之前钩子
     */
    public function beforeUpdate(object $oData):string
    {
        return $this->restrict($oData->activity_id,2) ? '' : "选手超过限制！请升级高级活动";
    }

    /**
     * @author yxx
     * @param int $id
     * @see 修改之后钩子
     */
    public function afterUpdate(object $oData)
    {
        $aData = $oData->toArray();
        $pushPlayer = get_push_player($aData['activity_id']);
        if ($pushPlayer && $pushPlayer['id'] == $aData['id'])
            $this->storePushPlayer($aData);
    }

    /**
     * @return bool
     * @see 清除选手得分
     */
    public function clearScoreAll()
    {
        $id = request()->post('id');
        if (!empty($id)) {
            $ids = explode(',',$id);
            $ids = array_map(function ($id){return hash_decode($id)??$id;},$ids);
        }
        if ($ids) {
            $aWhere['admin_id'] = adminId();
            $aWhere['activity_id'] = request()->input('activity_id') ?? '';
            $bUpdateRes = $this->where($aWhere)->whereIn('id',$ids)->update(['score'=>0]);
            if ($bUpdateRes) {
                // 清除redis 选手积分
                $aKeys = [];
                array_map(function ($id) use(&$aKeys){
                    $playerKey = config('tw.redis_key.h1').$id;
                    $aKeys[] = $playerKey;
                },$ids);
                Redis::del($aKeys);
            }
            return Tw::ajaxResponse("清除成功",$this->getIndexUrl());

        }
        return Tw::ajaxResponse("清除失败！");
    }

    /**
     * @param string $score
     * @param string $activity_id
     * @return string
     * @see 给首页推送评分完成的一个分数的url
     */
    public function getPushUrl(string $score,string $activity_id):string
    {
        $token   = hash_make(['finishScore',$activity_id,$score]);
        return $_SERVER['HTTP_HOST'].":9502?page=finishScore&activity_id=$activity_id&score=$score&token=".$token;
    }

    /**
     * @param string $score
     * @param string $activity_id
     */
    public function pushFinishScore(string $score,string $activity_id):void
    {
        curl_get($this->getPushUrl($score,$activity_id));
    }

}