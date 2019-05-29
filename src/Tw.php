<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 2:36 PM
 */
namespace Tw\Server;
class Tw
{
    use \Tw\Server\Traits\Tw;
    use \Tw\Server\Traits\HasAssets;

    /**
     * @var string
     */
    public static $metaTitle;

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
}