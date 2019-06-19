<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 10:44 AM
 */
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

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
if (!function_exists('get_activity_level')) {
    /**
     * @return array
     */
    function get_activity_level(int $key = null)
    {
        $aData = [
            1 => "普通活动",
            2 => "高级活动"
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
        //if( isset($get['activity_id'])   ){ unset($get['activity_id']);   }
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
/**
 * @Title: table_sort
 * @Description: todo(列表table排序)
 * @param string $param
 * @return string
 * @author yxx
 * @date 2017年8月21日
 * @throws
 */
if (!function_exists('table_sort' )) {
    function table_sort($param)
    {
        $url_path = request()->path();
        $get = $_GET;
        $faStr = 'fa-sort';
        if (isset($get['_pjax'])) {
            unset($get['_pjax']);
        }

        if (isset($get['_sort'])) {   //判断是否存在排序字段
            $sortArr = explode(',', $get['_sort']);
            if ($sortArr[0] == $param) {   //当前排序
                if ($sortArr[1] == 'asc') {
                    $faStr = 'fa-sort-asc';
                    $sort = 'desc';
                } elseif ($sortArr[1] == 'desc') {
                    $faStr = 'fa-sort-desc';
                    $sort = 'asc';
                }
                $get['_sort'] = $param . ',' . $sort;
            } else {   //非当前排序
                $get['_sort'] = $param . ',asc';
            }
        } else {
            $get['_sort'] = $param . ',asc';
        }
        $paramStr = [];
        foreach ($get as $k => $v) {
            $paramStr[] = $k . '=' . $v;
        }
        $paramStrs = implode('&', $paramStr);
        $url_path = $url_path . '?' . $paramStrs;
        return "<a class=\"fa " . $faStr . "\" href=\"" . asset($url_path) . "\"></a>";
    }
}


if (!function_exists('juheCurl' )) {
    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    function juheCurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

}
if (!function_exists('sendMsg' )) {
    /**
     * @param string $to
     */
    function sendMsg(string $to)
    {
        $rand = rand(100001, 999999);
        $params = array(
            'key' => config('tw.short_message')['key'],      //您申请的APPKEY
            'mobile' => $to,                                         //接受短信的用户手机号码
            'tpl_id' => config('tw.short_message')['tpl_id'],   //您申请的短信模板ID，根据实际情况修改
            'tpl_value' => '#code#=' . $rand                               //您设置的模板变量，根据实际情况修改
        );
        $ip = request()->getClientIp();

        $sToKey = config('tw.redis_key.s1') . $to;
        $sIpKey = config('tw.redis_key.s2') . $ip;
        if ($ip) {
            Redis::incr($sIpKey);
            Redis::expire($sIpKey, 180);
            if (Redis::get($sIpKey) && Redis::get($sIpKey) > 50)        // ip 三分钟超过50次 我们视为不正常操作
                throw new Exception("请勿恶意点击短信发送！");
        }
        Redis::incr($sToKey);
        Redis::expire($sToKey, 180);
        if (Redis::get($sToKey) && Redis::get($sToKey) > 3)             //三分钟只能发送三次
            throw new Exception("当前手机号短信发送过于频繁，三分钟以后在进行尝试！");

        $paramstring = http_build_query($params);
        $content = juheCurl("http://v.juhe.cn/sms/send", $paramstring);
        $result = json_decode($content, true);
        if ($result && $result['error_code'] == 0) {
            $sCodeKey = config('tw.redis_key.s3') . $to;
            Redis::set($sCodeKey, $rand);
            Redis::expire($sCodeKey, 300);                               // 验证码有效期5分钟
        } else if ($result && $result['reason']) {
            $sLogKey = config('tw.redis_key.s4');
            Redis::hset($sLogKey,$to.':'.date("Y-m-d H:i:s"),$content);   // 记录日志
            throw new Exception($result['reason']);
        } else {
            throw new Exception("短信发送异常！");
        }
    }
}
if (!function_exists('comparisonCode' )) {
    /**
     * @param string $code
     * @param string $key
     * @return bool
     * @see 手机验证码比对
     */
    function comparisonCode(string $code,string $key = null):bool
    {
        $bData = false;
        $sCodeKey = config('tw.redis_key.s3') . $key;
        $sRedisCode = Redis::get($sCodeKey);
        if (!empty($sRedisCode) && $sRedisCode == $code) {
            Redis::del($sCodeKey);
            $bData = true;
        }
        return $bData;
    }
}
if (!function_exists("hash_make")) {
    /**
     * @param array $params
     * @return string
     * @hash 加密
     */
    function hash_make(array $params):string
    {
        $params = implode("-",$params);
        return Hash::make($params);
    }

}

if (!function_exists("hash_check")) {
    /**
     * @param string $old
     * @param array $now
     * @return bool 检测是否对
     */
    function hash_check(string $old, array $now):bool
    {
        $now  = implode("-",$now);
        return Hash::check($now,$old);
    }
}

if (!function_exists("get_push_player")) {

    /**
     * @param int $activity_id
     * @return array
     * 获取当前推送的选手
     */
    function get_push_player(int $activity_id):array
    {
        $aData = [];
        $playerKey = config('tw.redis_key.h3');
        $field  = $activity_id;
        $player = Redis::hget($playerKey,$field);
        if ($player)
            $aData = json_decode($player, true);
        return $aData;

    }
}

if (!function_exists("del_push_player")) {
    /**
     * @param int $activity_id
     * @return mixed
     * @see 删除推送选手
     */
    function del_push_player(int $activity_id)
    {
        $playerKey = config('tw.redis_key.h3');
        return Redis::hdel($playerKey,$activity_id);
    }

}
if (!function_exists("tw_abort")) {
    /**
     * @param $title
     * @param $code
     * @return \Illuminate\Http\Response
     * @see 异常处理
     */
    function tw_abort($title,$code):object
    {
        return response()->view('tw::home.exception', ['title' => $title], $code);
    }

}
if (!function_exists("get_order_no")) {
    /**
     * @return string
     * @see 生成唯一单号
     */
    function get_order_no():string
    {
        return date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists("xss_json")) {
    /**
     * @param array $aInput
     * @return false|string
     * json xss过滤
     */
    function xss_json(array $aInput):string
    {
        safe_filter($aInput);
        return json_encode($aInput, JSON_UNESCAPED_UNICODE);
    }

}
if (!function_exists("safe_filter")) {
    /**
     * @param $arr
     * @see php防注入和XSS攻击通用过滤.
     */
    function safe_filter(&$arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                if (!is_array($value)) {
                    if (!get_magic_quotes_gpc()) {
                        $value    = addslashes($value);    //给单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）加上反斜线转义
                    }
                    $arr[$key]         = htmlspecialchars($value,ENT_QUOTES);   //&,",',> ,< 转为html实体 &,"',>,<
                } else {
                    safe_filter($arr[$key]);
                }
            }
        }
    }

}
if (!function_exists("tw_route")) {
    /**
     * @param $name
     * @param array $parameters
     * @param bool $absolute
     * @return string
     * @see hash_ids 加密
     */
    function tw_route($name,$parameters = [],$absolute = true)
    {
        if (!empty($parameters)) {
            if (is_int($parameters) || is_string($parameters)) {
                $parameters = Hashids::encodeHex($parameters);
            } elseif (is_array($parameters)) {

                foreach ($parameters as $key => $value) {
                    if (is_int($value) || is_string($value)) {
                        $parameters[$key] = Hashids::encodeHex($value);
                    }

                }
            }
        }
        return route($name,$parameters,$absolute);
    }
}

if (!function_exists("hash_encode")) {
    /**
     * @param string $id
     * @return string
     */
    function hash_encode(string $str):string
    {
        return Hashids::encodeHex($str);
    }
}

if (!function_exists("hash_decode")) {
    /**
     * @param string $id
     * @return string
     */
    function hash_decode(string $str):string
    {
        return Hashids::decodeHex($str);
    }
}

if (!function_exists("item_no")) {
    /**
     * @param int $item
     * @return int
     */
    function item_no(int $item):int
    {
        if (!empty(request()->input('page')))
        {
            $item = (request()->input('page') - 1) * config("tw.page.default") + $item;
        }
        return $item;
    }
}

if (!function_exists('curl_get')) {
    /**
     * @param string $url
     * @return string
     */
    function curl_get(string $url):string
    {
        $ch_curl = curl_init();
        curl_setopt ($ch_curl, CURLOPT_TIMEOUT_MS, 3000);
        curl_setopt($ch_curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch_curl, CURLOPT_HEADER,false);
        curl_setopt($ch_curl, CURLOPT_HTTPGET, 1);
        curl_setopt($ch_curl, CURLOPT_RETURNTRANSFER,true);
        curl_setopt ($ch_curl, CURLOPT_URL,$url);
        $str  = curl_exec($ch_curl);
        curl_close($ch_curl);
        return $str;
    }
}

if (!function_exists('adminId')) {

    function adminId()
    {
        return \Tw::authLogic()->guard()->id();
    }
}

if (!function_exists('redis_set')) {
    /**
     * @param $sKey
     * @param $content
     * @param int $time
     */
    function redis_set($sKey,$content,$time = 7200)
    {
        if (is_array($content) || is_object($content)) {
            Redis::set($sKey,json_encode($content,true));
        } else {
            Redis::set($sKey,$content);
        }
        Redis::expire($sKey,$time);
    }
}

if (!function_exists('redis_get')) {
    /**
     * @param $sKey
     * @param $content
     * @param int $time
     */
    function redis_get($sKey)
    {
        $sData = Redis::get($sKey);
        $content = json_decode($sData,true);
        if ($content && (is_object($content)) || (is_array($content) && !empty($content))) {
            $result = $content;
        } else {
            $result = $sData;
        }
        return $result;
    }
}











