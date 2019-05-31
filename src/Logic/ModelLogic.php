<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/31
 * Time: 3:47 PM
 */
namespace Tw\Server\Logic;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Support\Renderable;
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

    /**
     * @param string $id
     * @param array $data
     */
    public function update(string $id, array $data = [])
    {
        $data = ($data) ?: request()->all();
        dd('222');

    }
    /**
     * @return string|void
     */
    public function render()
    {
        dd("this is render func");
    }



}
