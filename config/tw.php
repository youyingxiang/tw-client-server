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
            'title' => "个人中心",
            "icon"  => "fa-user-plus",
            "url"   => "",
            "order" => 1,
            "parent_id"   => 0,
        ],
        [
            "id"    => 2,
            'title' => "活动管理",
            "icon"  => "fa-circle-o",
            "url"   => "",
            "order" => 2,
            "parent_id"   => 0,
        ],
        [
            "id"    => 3,
            'title' => "个人列表",
            "icon"  => "fa-users",
            "url"   => "/userinfo",
            "order" => 1,
            "parent_id"   => 1,
        ],
        [
            "id"    => 4,
            'title' => "消费记录",
            "icon"  => "fa-circle-o",
            "url"   => "/tw-server",
            "order" => 1,
            "parent_id"   => 1,
        ],
        [
            "id"    => 5,
            'title' => "活动列表",
            "icon"  => "fa-circle-o",
            "url"   => "/activity",
            "order" => 1,
            "parent_id"   => 2,
        ],
        [

            "id"    => 6,
            'title' => "选手列表",
            "icon"  => "fa-circle-o",
            "url"   => "/player",
            "order" => 1,
            "parent_id"   => 2,
        ],
        [

            "id"    => 7,
            'title' => "评委列表",
            "icon"  => "fa-circle-o",
            "url"   => "/judges",
            "order" => 1,
            "parent_id"   => 2,
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
        ],
    ],

    'database' => [
        'connection' => '',
        'admin_table' => 'tw_admin',
    ],
    'restrict' => [
        'player'    => 10,      //普通用户限制选手个数
        'judges'    => 5,       //普通用户限制评委个数
    ],
    // 短信配置
    'short_message' => [
        'key'    => 'facf957222f2638085762e47bd7303c1', // 短信app_key
        'tpl_id' => '159713',                           // 短信模版id
    ],

    'upload_url' => "/yxx/kindeditor/upload?type=image",

    'redis_key'  => [
        'h1' => 'hash:score:player:',   // hash 选手得分
        'h2' => 'hash:tw:swoole',       // hash websocket 链接信息
        'h3' => 'hash:push_player',     // hash 活动被推送的选手
        's1' => "str:send_msg:phone:",  // 电话 短信发送次数
        's2' => "str:send_msg:ip:",     // 短信发送ip记录次数
        's3' => "str:send_msg:code:",   // 短信验证码
        's4' => "str:send_msg:log"      // 短信异常日志
    ],
];