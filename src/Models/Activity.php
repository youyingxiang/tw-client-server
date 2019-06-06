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
    protected $and_fields = ['days'];


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

    public function getTermAttribute():string
    {
        return $this->created_at." 至 ".
            date('Y-m-d H:i:s',strtotime("+".$this->days."day",strtotime($this->created_at)));
    }


}
