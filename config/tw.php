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
            'title' => "活动列表",
            "icon"  => "fa-circle-o",
            "url"   => "",
            "order" => 2,
            "parent_id"   => 0,
        ],
        [
            "id"    => 3,
            'title' => "个人列表",
            "icon"  => "fa-users",
            "url"   => "/tw-server",
            "order" => 1,
            "parent_id"   => 1,
        ],
    ],
    'route' => [

        'prefix' => 'twserver',

        'namespace' => 'Tw\\Server\\Controllers',

        'middleware' => ['web'],
    ],

    'https' => env('TW_HTTPS', false),
];