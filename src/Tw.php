<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:36 PM
 */
namespace Tw\Server;
use Tw\Server\Logic\AuthLogic as AuthLogic;
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

    public function authLogic():AuthLogic
    {
       return new AuthLogic();
    }
}