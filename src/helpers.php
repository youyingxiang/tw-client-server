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
        return response()->json($result);
    }
}

if (!function_exists('get_score_type')) {
    /**
     * @return array
     */
    function get_score_type(int $key = null)
    {
        $aData = [
            1 => "取平均算法",
            2 => "去最大和最小算法"
        ];
        return $key ? $aData[$key] : $aData;
    }
}

if (!function_exists('button')) {
    /**
     * @param string $url
     * @param string $type
     * @return string
     */
    function button(string $url = null ,string $type = null,string $id = null):string
    {
        return Tw::newTool('Button',
            [
                'id'   => $id,
                'url'  => $url,
                'type' => $type,
            ]
        )->getbutton();
    }
}
if (!function_exists('search_url' )) {
    function search_url($delparam)
    {
        $url_path = request()->path();
        $get = $_GET;
        if( isset($get[$delparam]) ){ unset($get[$delparam]); }
        if( isset($get['_pjax'])   ){ unset($get['_pjax']);   }
        if( isset($get['page'])   ){ unset($get['page']);   }
        if( isset($get['activity_id'])   ){ unset($get['activity_id']);   }
        if (!empty($get)) {
            $paramStr = [];
            foreach ($get as $k => $v) {
                $paramStr[] = $k . '=' . $v;
            }
            $paramStrs = implode('&', $paramStr);
            $url_path = $url_path . '?' . $paramStrs;
        }
        if (!(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
            $url_path = ltrim($url_path, DIRECTORY_SEPARATOR);
            $url_path = '/' . $url_path;
        }
        return $url_path;
    }
}

