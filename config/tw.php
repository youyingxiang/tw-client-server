<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 1:38 PM
 */
return [
    // 菜单栏
    'tw_server_menu' => [
        [
            "id"    => 1,
            'title' => "活动管理",
            "icon"  => "fa-gear",
            "url"   => "/activity?level=1",
            "order" => 1,
            "parent_id"   => 0,
        ],
        [
            "id"    => 2,
            'title' => "消费记录",
            "icon"  => "fa-navicon",
            "url"   => "/tw-server",
            "order" => 2,
            "parent_id"   => 0,
        ],

    ],
    'route' => [

        'prefix' => 'twserver',

        'namespace' => 'Tw\\Server\\Controllers',

        'middleware' => ['web','tw'],
    ],

    'https' => env('TW_HTTPS', false),

    'auth' => [

        'controller' => Tw\Server\Controllers\AuthController::class,

        'guards' => [
            'tw' => [
                'driver'   => 'session',
                'provider' => 'tw',
            ],
        ],

        'providers' => [
            'tw' => [
                'driver' => 'eloquent',
                'model'  => Tw\Server\Models\Admin::class,
            ],
        ],

        // Add "remember me" to login form
        'remember' => true,

        // Redirect to the specified URI when user is not authorized.
        'redirect_to' => 'login',

        // The URIs that should be excluded from authorization.
        'excepts' => [
            'login',
            'logout',
            'register',
            'sendMsg',
            'rest',
            'resetSendMsg',
            'checkCode',
        ],
    ],

    'database' => [
        'connection' => '',
        'admin_table' => 'tw_admin',
    ],
    'restrict' => [
        'player'    => 10,      //普通用户限制选手个数
        'judges'    => 5,       //普通用户限制评委个数
        'activity'  => 5,       //普通项目限制添加数量
        'norelease_player' => 2, //未发布活动限制选手个数
        'norelease_judges' => 2, //未发布评委个数
    ],
    // 短信配置
    'short_message' => [
        'key'    => 'facf957222f2638085762e47bd7303c1', // 短信app_key
        'tpl_id' => '164158',                           // 短信模版id
    ],

    'upload_url' => "/yxx/kindeditor/upload?type=image",

    'redis_key'  => [
        'h1' => 'hash:score:player:',   // hash 选手得分
        'h2' => 'hash:tw:swoole',       // hash websocket 链接信息
        'h3' => 'hash:push_player',     // hash 活动被推送的选手
        'h4' => 'hash:login_judges_sessionid', // 登陆评委的sessionid 检测是不是同一个人登陆
        's1' => "str:send_msg:phone:",  // 电话 短信发送次数
        's2' => "str:send_msg:ip:",     // 短信发送ip记录次数
        's3' => "str:send_msg:code:",   // 短信验证码
        's4' => "str:send_msg:log",      // 短信异常日志
        's5' => "str:flashdata:admin_id:", // 闪存数据
        "hset1" => "set:login_judges_id"     // 扫码登陆过的评委id
    ],
    'pay_amount_base' => [
        'senior' => 0.01,   // 开通高级活动的钱
        'oneday' => 0.01,   // 续费一天的钱
    ],
    "page" => [
        "default" => 100,
    ],
];