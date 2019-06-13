<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/6/5
 * Time: 10:23 AM
 */
namespace Tw\Server\Models;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Facades\Redis;
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
        $url = empty(request()->get('activity_id'))
            ? route('tw.judges.index')
            : route('tw.judges.index')."?activity_id=".request()->get('activity_id');
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
        return QrCode::size(100)->color(255,0,255)
            ->backgroundColor(255,255,0)
            ->generate(route("tw.home.judges",$this->id));
    }

    /**
     * @return array
     * @see 普通用户 最多添加n个评委
     */
    public function restrict(string $activity_id):bool
    {
        $bFlag = false;
        $limit = config('tw.restrict.judges',5);
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
     * @param $id 评委id
     * @return array
     */
    public function getPlayerByRedis(int $id):array
    {
        $oJudges = $this->find($id);
        return get_push_player($oJudges['activity_id']);
    }

}