<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:37 PM
 */
namespace Tw\Server\Traits;
use Illuminate\Support\Arr;
use Tw\Server\Controllers\IndexController;

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
                $router->resource('tw-server', 'TwServerController')->names('tw.index');
                $router->get('login', 'AuthController@getLogin')->name('tw.login');
                $router->post('login', 'AuthController@postLogin');
                $router->get('rest', 'AuthController@getRestPassWord')->name('tw.resetpassword');
                $router->post('rest', 'AuthController@postRestPassWord');
                $router->get('logout', 'AuthController@getLogout')->name('tw.logout');
                $router->get('register','AuthController@getRegister')->name('tw.register');
                $router->post('register','AuthController@postRegister');
                $router->post('sendMsg','AuthController@sendMsg')->name('tw.sendmsg');
                $router->post('resetSendMsg','AuthController@resetSendMsg')->name('tw.resetSendMsg');
                $router->post('checkCode','AuthController@checkCode')->name('tw.checkCode');
                $router->get('userinfo', 'AdminController@getUserinfo')->name('tw.userinfo');
                $router->post('userinfo', 'AdminController@postUserinfo')->name("tw.userinfo.update");
                $router->resource('activity', 'ActivityController')->names('tw.activity')->middleware('tw.hashids');
                $router->get('release/{id}', 'ActivityController@release')->name('tw.activity.release')->middleware('tw.hashids');
                $router->get('control/{id}', 'ActivityController@control')->name('tw.activity.control')->middleware('tw.hashids');
                $router->resource('judges', 'JudgesController')->names('tw.judges')->middleware('tw.hashids');
                $router->delete('clearlink/{id}', 'JudgesController@clearLink')->name('tw.judges.clearlink')->middleware('tw.hashids');
                $router->delete('clear_score_all/{id}','PlayerController@clearScoreAll')->name("tw.player.clearall")->middleware('tw.hashids');
                $router->resource('player', 'PlayerController')->names('tw.player')->middleware('tw.hashids');
                $router->get('playerpush/{id}', 'PlayerController@push')->name('tw.player.push')->middleware('tw.hashids');
                $router->get('nextPlayer/{activity_id}', 'ActivityController@nextPlayer')->name('tw.player.nextPlayer');
                $router->post('storeorder','PayOrderController@storeOrder')->name('tw.payorder.store');
                $router->get('qrcode/{order_no}','PayOrderController@qrCode')->name('tw.payorder.qrcode');
                $router->get('check_order_state/{order_no}','PayOrderController@checkOrderState')->name('tw.payorder.check');

            });
        });
        app('router')->namespace(config('tw.route.namespace'))->group(function ($router) {
            $router->get('activity/{activityId}', "HomeController@index")->name('tw.home')->middleware('tw.hashids');
            $router->get('judges/{judgesId}',"HomeController@judges")->name('tw.home.judges')->middleware('tw.hashids','web');
            $router->post('postScoring',"HomeController@postScoring")->name('tw.home.postScoring');
            $router->get('rank/{activityId}',"HomeController@rank")->name("tw.home.rank")->middleware('tw.hashids');
            $router->any('wechat_notify',"PayOrderController@wechatNotify")->name("tw.payorder.notify");
            $router->get("judgeslinkerr/{type}",function ($type){
                if ($type == 1)
                    return  tw_abort("请重新进行扫码登陆！",403);
                if ($type == 2)
                    return  tw_abort("评委已经处于连接状态！",403);
            })->name('tw.home.judgeslinkerr');
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

    protected static function loadAdminAuthConfig()
    {
        config(Arr::dot(config('tw.auth', []), 'auth.'));
    }

    public static function register():void
    {
        self::loadAdminAuthConfig();
    }
}