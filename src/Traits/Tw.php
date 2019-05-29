<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:37 PM
 */
namespace Tw\Server\Traits;
trait Tw
{
    public static $commands = [
        \Tw\Server\Console\Swoole::class
    ];

    public static function boot():void
    {
        static::registerRoutes();
    }

    protected static function registerRoutes():void
    {
        $attributes = [
            'prefix'     => config('tw.route.prefix','tw'),
            'middleware' => config('tw.route.middleware'),
        ];
        app('router')->group($attributes, function ($router) {
            $router->namespace(config('tw.route.namespace'))->group(function ($router) {
                $router->resource('tw-server/', 'TwServerController')->names('tw.index');
            });
        });
    }


    /**
     * @describe 获取当前服务视图的包
     * @return string
     */
    public static function  getLoadViewPath():array
    {
        return ["path"=>__DIR__.'/../../resources/views',"alias"=>'tw'];
    }
    public static function register():void
    {

    }
}