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

    protected $oModelResult;

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
    public function update(string $id, array $data = [])
    {
        $bSaveRes = false;
        $aData = ($data) ?: request()->all();
        $this->oModelResult = $this->model::findOrFail($id);
        DB::transaction(function () use ($aData,&$bSaveRes) {
            $aUpdates = $this->prepare($aData);
            foreach ($aUpdates as $column => $value) {
                /* @var Model $this->model */
                $this->oModelResult->setAttribute($column, $value?:'');
            }
            $bSaveRes = $this->oModelResult->save();
        });
        if ($bSaveRes)
            return Tw::ajaxResponse("操作成功",$this->oModelResult->getIndexUrl());;
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




    /**
     * @return string|void
     */
    public function render()
    {
        dd("this is render func");
    }



}
