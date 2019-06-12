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
        return route('tw.player.index');
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
     * @return array
     * @see 普通用户 最多添加n个选手
     */
    public function restrict(string $activity_id):bool
    {
        $bFlag = false;
        $limit = config('tw.restrict.player',10);
        $activityInfo = Tw::newModel('Activity')->where('admin_id',Tw::authLogic()->guard()->id())->find($activity_id);
        if (isset($activityInfo['level']) && $activityInfo['level'] == 1) {
            $players = $this->where(['admin_id' => Tw::authLogic()->guard()->id(),'activity_id'=>$activity_id])->count();
            if ($players > 0)
                $bFlag =  $limit > $players;
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
            return Tw::ajaxResponse("操作成功",$this->getIndexUrl());
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
            Redis::del(config('tw.redis_key.h1').$player['id']);
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


}