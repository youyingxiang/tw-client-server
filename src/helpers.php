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

if (!function_exists('ajaxReturn')) {
    /**
     * @Title: ajaxReturn
     * @Description: todo(ajax提交返回状态信息)
     * @param string $info
     * @param url $url
     * @param string $status
     * @author yxx
     * @date 2016-5-12
     */
    function ajaxReturn($info = '', $url = '', $status = '', $data = '')
    {
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
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit();
    }
}