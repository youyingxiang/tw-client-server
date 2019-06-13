<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/31
 * Time: 3:47 PM
 */
namespace Tw\Server\Logic;
use Closure;
use Tw\Server\Facades\Tw;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Contracts\Support\Renderable;
class ModelLogic implements Renderable
{
    /**
     * @var
     */
    protected $model;


    /**
     * @var
     */
    protected $oModelResult;
    /**
     * @var
     */
    protected $aDataScore;

    /**
     * ModelLogic constructor.
     * @param $model
     * @param Closure|null $callback
     */
    public function __construct($model, Closure $callback = null)
    {
        $this->model = $model;
        if ($callback instanceof Closure) {
            $callback($this);
        }
    }
    /**
     * @return object
     */
    public function getModel():object
    {
        return $this->model;
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update(string $id, array $aData = [],array $aWhereData = [])
    {
        $bSaveRes = false;
        $where   = $this->getWhere($aWhereData);
        $this->oModelResult = $this->model->where($where)->findOrFail($id);
        DB::transaction(function () use ($aData,&$bSaveRes,$id) {
            if (method_exists($this->model,'beforeUpdate')) {
                call_user_func([$this->model,'beforeUpdate'],$id);
            }
            $aUpdates = $this->prepare($aData);
            foreach ($aUpdates as $column => $value) {
                /* @var Model $this->model */
                $this->oModelResult->setAttribute($column, is_null($value)?'':$value);
            }
            $bSaveRes = $this->oModelResult->save();

            if (method_exists($this->model,'afterUpdate')) {
                call_user_func([$this->model,'afterUpdate'],$this->oModelResult);
            }
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->oModelResult->getIndexUrl());
        else
            return Tw::ajaxResponse("操作失败");

    }

    /**
     * @param array $aData
     */
    public function store(array $aData = [])
    {
        if (method_exists($this->model,'restrict') && !empty($aData['activity_id'])) {
            $res = call_user_func([$this->model, 'restrict'],$aData['activity_id']);
            if (false == $res)
                return Tw::ajaxResponse("添加人数超过限制！,请把活动升级为高级活动");
        }
        $bSaveRes = false;
        DB::transaction(function ()use($aData,&$bSaveRes) {
            foreach ($aData as $column => $value) {
                /* @var Model $this->model */
                $this->model->setAttribute($column, is_null($value)?'':$value);
            }
            $bSaveRes =  $this->model->save();
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->model->getIndexUrl());
        else
            return Tw::ajaxResponse("操作失败");
    }

    /**
     * @param array $whereAdata
     */
    public function query(array $aWhereData = []):object
    {
        $where   = $this->getWhere($aWhereData);
        $orWhere = $this->getOrWhere($aWhereData);
        if (!empty($aWhereData['_sort'])) {
            $aOrder =  explode(',', $aWhereData['_sort']);
        } else {
            $aOrder = ['id','desc'];
        }
//        DB::enableQueryLog();
        $aData = $this->model->where($where)->where($orWhere)->orderBy($aOrder[0],$aOrder[1])->paginate(15);
//        dd((DB::getQueryLog()));
        return $aData;
    }

    /**
     * @param array $aWhereData
     */
    public function find(int $id,array $aWhereData = [])
    {
        $where   = $this->getWhere($aWhereData);
        $orWhere = $this->getOrWhere($aWhereData);
        $oData = $this->model->where($where)->where($orWhere)->find($id);
        return $oData;
    }

    /**
     * @param string $id
     * @param $aWhereData
     */
    public function destroy(string $id,$aWhereData = [])
    {
        $bSaveRes = false;
        if (!empty($id)) {
            $ids = explode(',',$id);
        }
        $where = $this->getWhere($aWhereData);
        DB::transaction(function ()use($where,$ids,&$bSaveRes) {
            if (method_exists($this->model,'beforeDelete')) {
                call_user_func([$this->model,'beforeDelete'],$ids);
            }
            $bSaveRes = $this->model->where($where)->whereIn('id',$ids)->delete();
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->model->getIndexUrl());
        else
            return Tw::ajaxResponse("操作失败");
    }

    /**
     * @param $search
     * @return Closure
     */
    public function getWhere(array $aWhere):Closure
    {

        $where = function ($query) use ($aWhere) {
            /**
             * 当前项目归属谁
             */
            if (method_exists($this->model,'parentFlag') && !empty($this->model->parentFlag())) {
                foreach ($this->model->parentFlag() as $pk=> $pv) {
                    $query->where($pk,$pv);
                }
            }
            // and where
            if (method_exists($this->model,'getAndFieds') && $aWhere &&
                $this->model->getAndFieds()
            ) {
                foreach ($aWhere as $field => $value) {
                    if (in_array($field,$this->model->getAndFieds()) && isset($value)) {
                        $query->where($field,$value);
                    }

                }
            }
        };
        return $where;
    }


    /**
     * @param $aWhere
     * @return Closure
     */
    public function getOrWhere($aWhere) :Closure
    {
        $where = function ($query) use ($aWhere) {
            // or where
            if (method_exists($this->model,'getOrFields') && !empty($aWhere['search']) &&
                $this->model->getOrFields()
            ) {
                foreach ($this->model->getOrFields() as $field) {
                    $query->orWhere($field, "like", "%" . $aWhere['search'] . "%");
                }
            }
        };
        return $where;
    }





    /**
     * @param array $aData
     * @return array
     */
    protected function prepare($aData = [])
    {
        $this->relations = $this->getRelationInputs($aData);
        $aData = Arr::except($aData,['id']);
        return Arr::except($aData, array_keys($this->relations));
    }

    /**
     * @param array $inputs
     * @return array
     */
    protected function getRelationInputs($aData = []):array
    {
        $relations = [];

        foreach ($aData as $column => $value) {
            if (!method_exists($this->oModelResult, $column)) {
                continue;
            }

            $relation = call_user_func([$this->oModelResult, $column]);

            if ($relation instanceof Relations\Relation) {
                $relations[$column] = $value;
            }
        }

        return $relations;
    }

    /**
     * @param array $aData
     * @see 为防止redis key过多
     * 在没有得分时候 score:player:选手id  field=评委id   value=评委打出的分数
     * 获取得分以后   score:activity:活动id  field=选手id  value = json(key="评委id",value="评委打出的分数"）
     */
    public function storeScore($aData = []):void
    {
        $this->aDataScore = $aData;
        $this->playerKey = config('tw.redis_key.h1').$this->aDataScore['player_id'];
        $field     = $this->aDataScore['judges_id'];
        $score     = $this->aDataScore['score'];
        // 判断是否已经进行进行过评分
        if (Redis::hexists($this->playerKey,$field))
            throw new \Exception("您已经对当前选手进行过评分！无需再次评分");
        if ($score < 0)
            throw new \Exception("请输入正确的分数！");
        Redis::hset($this->playerKey,$field,$score);
        // 获取有多少评委
        $iSumJudges = $this->model->where('activity_id',$this->aDataScore['activity_id'])->count();
        // 当前所有评委都已经打分完毕
        if ($iSumJudges > 0 && $iSumJudges == Redis::hlen($this->playerKey)) {
            $this->scoreType();
//            $activityKey = "score:activity:".$this->aDataScore['activity_id'];
//            $aValue = Redis::hgetall($this->playerKey);
//            Redis::hset($activityKey,$this->aDataScore['player_id'],json_encode($aValue,true));
//            Redis::del($this->playerKey);
        }
    }

    /**
     * @see 设置分数
     */
    public function scoreType():void
    {
        $oData = Tw::newModel("Activity")->find($this->aDataScore['activity_id']);
        $scoreType = $oData['score_type'];
        // 1 取平均 2 去掉最大最小
        if ($scoreType == 1) {
            $score = $this->avgScore();
        } elseif ($scoreType == 2) {
            $score = $this->removeMinAndMaxScore();
        }
        Tw::newModel("Player")->where('id',$this->aDataScore['player_id'])->update(['score'=>$score]);
    }

    /**
     * @param array $aData
     * @see 平均算法
     */
    public function avgScore():float
    {
        $aScores = Redis::hvals($this->playerKey);
        return round(array_sum($aScores)/count($aScores),2);
    }
    /**
     * @param array $aData
     * @see 去掉最大值最小值算法
     */
    public function removeMinAndMaxScore():float
    {
        $aScores = Redis::hvals($this->playerKey);
        sort($aScores);
        array_pop($aScores);
        array_shift($aScores);
        return round(array_sum($aScores)/count($aScores),2);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (method_exists($this->model,$name)) {
            $jResult = call_user_func([$this->model, $name],$arguments);
            return $jResult;
        } else {
            abort(404);
        }

    }

    /**
     * @return string|void
     */
    public function render()
    {
        dd("this is render func");
    }



}
