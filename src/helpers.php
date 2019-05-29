<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 10:44 AM
 */
use Illuminate\Support\MessageBag;
if (!function_exists('tw_base_path')) {
    /**
     * Get admin url.
     *
     * @param string $path
     *
     * @return string
     */
    function tw_base_path($path = '')
    {
        $prefix = '/'.trim(config('tw.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        $path = trim($path, '/');

        if (is_null($path) || strlen($path) == 0) {
            return $prefix ?: '/';
        }

        return $prefix.'/'.$path;
    }
}

if (!function_exists('tw_asset')) {

    /**
     * @param $path
     *
     * @return string
     */
    function tw_asset($path)
    {
        return (config('tw.https') || config('tw.secure')) ? secure_asset($path) : asset($path);
    }
}