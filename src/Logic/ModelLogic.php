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
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Relations;
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
        DB::transaction(function () use ($aData,&$bSaveRes) {
            $aUpdates = $this->prepare($aData);
            foreach ($aUpdates as $column => $value) {
                /* @var Model $this->model */
                $this->oModelResult->setAttribute($column, !is_null($value)?:'');
            }
            $bSaveRes = $this->oModelResult->save();
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
                $this->model->setAttribute($column, !is_null($value)?:'');
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
        $aData = $this->model->where($where)->where($orWhere)->orderBy($aOrder[0],$aOrder[1])->paginate(10);
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
        if (!empty($id)) {
            $ids = explode(',',$id);
        }
        $where = $this->getWhere($aWhereData);
        $bSaveRes = $this->model->where($where)->whereIn('id',$ids)->delete();
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
            if (!empty($this->model->parentFlag())) {
                foreach ($this->model->parentFlag() as $pk=> $pv) {
                    $query->where($pk,$pv);
                }
            }
            // and where
            if ($aWhere &&
                $this->model->getAndFieds()
            ) {
                foreach ($aWhere as $field => $value) {
                    in_array($field,$this->model->getAndFieds()) &&
                    $query->where($field,$value);
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
            if (!empty($aWhere['search']) &&
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
