<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:36 PM
 */
namespace Tw\Server;
use Closure;
use Tw\Server\Logic\AuthLogic;
use Tw\Server\Logic\ModelLogic;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
class Tw
{
    use \Tw\Server\Traits\Tw;
    use \Tw\Server\Traits\HasAssets;

    /**
     * @var string
     */
    public static $metaTitle;

    /**
     * @var array
     */
    protected $menu = [];


    /**
     * @param string $title
     */
    public static function setTitle(string $title):void
    {
        self::$metaTitle = $title;
    }
    /**
     * @return string
     */
    public static function getTitle():string
    {
        return self::$metaTitle ? self::$metaTitle : "tw-server";
    }

    /**
     * @return array
     */
    public function getMenu():array
    {
        if (!empty($this->menu)) {
            return $this->menu;
        }
        return $this->menu = (new \Tw\Server\Logic\MenuLogic())->reSort(config("tw.tw_server_menu"));
    }

    /**
     * @return AuthLogic
     */
    public function authLogic():AuthLogic
    {
       return new AuthLogic();
    }

    /**
     * @param $model
     * @param Closure $callable
     * @return modelLogic
     */
    public function moldelLogic($model, Closure $callable = null)
    {
        return new modelLogic($this->getModel($model), $callable);
    }

    /**
     * @param string $modelName
     * @return mixed
     */
    public function newModel(string $modelName)
    {
        $className = "\\Tw\\Server\\Models\\".$modelName;
        return new $className;
    }

    /**
     * @param $toolName
     * @param array $aParam
     * @return string
     */
    public function newTool($toolName,array $aParam = []):object
    {
        $className = "\\Tw\\Server\\Tool\\".$toolName;
        return new $className($aParam);
    }


    /**
     * @param $model
     * @return mixed
     */
    public function getModel($model)
    {
        if ($model instanceof Model) {
            return $model;
        }

        if (is_string($model) && class_exists($model)) {
            return $this->getModel(new $model());
        }

        throw new InvalidArgumentException("$model is not a valid model");
    }

    /**
     * Get ajax response.
     *
     * @param string $message
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function ajaxResponse($info = '', $url = '', $status = '', $data = '')
    {
        $request = Request::capture();
        // ajax but not pjax
        if ($request->ajax() && !$request->pjax()) {
            if (!empty($url)) {   //操作成功
                $result = array('info' => '操作成功', 'status' => 1, 'url' => $url,);
            } else {   //操作失败
                $result = array('info' => '操作失败', 'status' => 0, 'url' => '',);
            }
            if (!empty($info)) {
                $result['info'] = $info;
            }
            if (!empty($status)) {
                $result['status'] = $status;
            }
            if (!empty($data)) {
                $result['data'] = $data;
            }
            return response()->json($result);
        } else {
            return false;
        }
    }
}