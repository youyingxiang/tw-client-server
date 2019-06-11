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

    /**
     * @return string
     */
    public function getTermAttribute():string
    {
        if ($this->is_term)
            $sData = '<span style="color: green">'.$this->created_at." 至 ".$this->term_date.'</span>';
        else
            $sData = '<span style="color: red">已过期</span>';
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
        return date('Y-m-d H:i:s',strtotime("+".($days ?? $this->days)."day",strtotime(($time ?? $this->created_at))));
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
            $dResult = $this->getTermDate($oData['days'],$oData['created_at']);
            if ($this->IsTerm($dResult)) {
                $object = $oData;
            }
        }
       return $object;
    }



}
