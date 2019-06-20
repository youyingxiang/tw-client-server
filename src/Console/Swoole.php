<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 4:13 PM
 */
namespace Tw\Server\Console;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tw:swoole';
    /**
     * @var string
     */
    private  $key        = '^manks.top&swoole$';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "this is websocket push client";
    /**
     * @var
     */
    protected $redis;
    /**
     * @var null
     */
    private static $server = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected static  function setWebSocketServer():void
    {
        self::$server  = new \swoole_websocket_server("0.0.0.0", 9502);
        self::$server->set([
            'worker_num' => 1,
            'heartbeat_check_interval' => 60,    // 60秒检测一次
            'heartbeat_idle_time' => 121,        // 121秒没活动的
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->redis = Redis::connection('websocket');
        $server = self::getWebSocketServer();
        $server->on('open',[$this,'onOpen']);
        $server->on('message', [$this, 'onMessage']);
        $server->on('close', [$this, 'onClose']);
        $server->on('request', [$this, 'onRequest']);
        $this->line("swoole服务启动成功 ...");
        $server->start();

    }


    /**
     * 获取server实列
     * @return
     */
    public static function getWebSocketServer()
    {
        if (!(self::$server instanceof \swoole_websocket_server)) {
            self::setWebSocketServer();
        }
        return self::$server;
    }

    /**
     * @param $server
     * @param $request
     */
    public function onOpen($server, $request)
    {
        if ($this->checkAccess($server, $request))
            self::$server->push($request->fd, json_encode(["message"=>"swoole已经打开.."]));
    }
    /**
     * @param $serv
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        $aData = json_decode($frame->data,true);
        if (isset($aData['type'])) {
            $sFuncName = $this->enum((int)$aData['type']);
            if ($sFuncName && method_exists($this,$sFuncName)) {
                call_user_func([$this,$sFuncName],$aData);
            }
        }
    }

    /**
     * @param int $type
     * @return string
     * @see 枚举映射
     */
    public function enum(int $type):string
    {
        $aFunc = [
            1 => "pushPlayer",
            2 => "judgesScore",
            3 => "jumpRank",
            4 => "jumpHome"
        ];
        return $aFunc[$type] ?? '';
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request,$response)
    {
        if ($this->checkAccess("", $request)) {
            $param = $request->get;
            if (isset($param['page'])) {
                if (method_exists($this,$param['page'])) {
                    call_user_func([$this,$param['page']],$request);
                }
            }
        }
    }

    /**
     * @param array $aData
     * @see 根据推送返回Id 查询选手信息 找到属于活动 只推送给对应活动
     */
    public function pushPlayer(array $aData)
    {
        $aResult = DB::table('tw_player')->find($aData['player']);
        if ($aResult) {
            $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($aData,$aResult) {
                // 推送首页和评委页面
                if ($aContent && ($aContent['page'] == "home" || $aContent['page'] =="judges") ) {
                    if ($aResult->activity_id == $aContent['activity']) {
                        $aRes['judges_score'] = Redis::hgetall(config('tw.redis_key.h1').$aData['player']);
                        $aRes['player'] = (array)$aResult;
                        $oSwoole::$server->push($fd, xss_json($aRes));
                    }
                }
            };
            $this->eachFdLogic($callback);
        }
    }

    /**
     * @param array $aData
     * @see 评委打分逻辑
     */
    public function judgesScore(array $aData)
    {
        $aResult = DB::table('tw_player')->find($aData['player']);
        if ($aResult) {
            $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($aData,$aResult) {
                if ($aContent && $aContent['page'] == "home") {
                    if ($aResult->activity_id == $aContent['activity']) {
                        $hData['judges_score'] = Redis::hgetall(config('tw.redis_key.h1').$aData['player']);
                        $oSwoole::$server->push($fd, xss_json($hData));
                    }
                }
            };
            $this->eachFdLogic($callback);
        }
    }

    /**
     * @param array $aData
     * @see 跳转排名
     */
    public function jumpRank(array $aData)
    {
        $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($aData) {
            if ($aContent && $aContent['page'] == "rank") {
                if (isset($aData['activity']) && $aData['activity'] == $aContent['activity']) {
                    $aRes['url'] = tw_route('tw.home',(int)$aData['activity']);
                    $oSwoole::$server->push($fd,xss_json($aRes));
                }
            }
        };
        $this->eachFdLogic($callback);
    }

    /**
     * @param array $aData
     * @see 跳转首页
     */
    public function jumpHome(array $aData)
    {
        $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($aData) {
            if ($aContent && $aContent['page'] == "home") {
                if (isset($aData['activity']) && $aData['activity'] == $aContent['activity']) {
                    $aRes['url'] = tw_route('tw.home.rank',(int)$aData['activity']);
                    $oSwoole::$server->push($fd,xss_json($aRes));
                }
            }
        };
        $this->eachFdLogic($callback);
    }



    /**
     * @param $request
     * @see 微信回调成功触发订单逻辑
     */
    public function orderLogic($request)
    {
        $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($request) {
            if ($aContent['page'] == "qrcode"
                && $aContent['admin'] == $request->get['admin_id']
                && $aContent['order'] == $request->get['order']
            ){
                $aData = [
                    'state' => 1,
                    'info' => '操作成功!',
                    'url' => route("tw.index.index"),
                ];
                $oSwoole::$server->push($fd,xss_json($aData));
            }
        };
        $this->eachFdLogic($callback);
    }

    /**
     * @param $request
     * @see 完成评分
     */
    public function finishScore($request)
    {
        $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($request) {
            if ($aContent['page'] == "home"
            && isset($aContent['activity'])
            && isset($request->get['activity_id'])
            && $aContent['activity'] == $request->get['activity_id']
            ) {
                $aData = [
                    'state' => 1,
                    'info' => '评分完成',
                    'score' => $request->get['score']??'',
                ];
                $oSwoole::$server->push($fd,xss_json($aData));
            }
        };
        $this->eachFdLogic($callback);
    }

    /**
     * @param $request
     * @see 循环逻辑处理
     */
    public function eachFdLogic(Closure $callback = null)
    {
        foreach (self::$server->connections as $fd) {
            if (self::$server->isEstablished($fd)) {
                $aContent = json_decode($this->redis->hget(config('tw.redis_key.h2'),$fd),true);
                $callback($aContent,$fd,$this);
            } else {
                $this->redis->hdel(config('tw.redis_key.h2'),$fd);
            }
        }
    }

    /**
     * @param $serv
     * @param $fd
     */
    public function onClose($serv, $fd)
    {
        $this->redis->hdel(config('tw.redis_key.h2'),$fd);
        $this->line("客户端 {$fd} 关闭");
    }


    /**
     * 校验客户端连接的合法性,无效的连接不允许连接
     * @param $serv
     * @param $request
     * @return mixed
     */
    public function checkAccess($server, $request)
    {
        // get不存在或者uid和token有一项不存在，关闭当前连接
        if (!isset($request->get) || !isset($request->get['token'])) {
            self::$server->close($request->fd);
            $this->line("接口验证字段不全");
            return false;
        }
        $aData = Arr::except($request->get,"token");
        // 校验token是否正确,无效关闭连接
        if (hash_check($request->get['token'],$aData) == false) {
            $this->line("接口验证错误");
            self::$server->close($request->fd);
            return false;
        } else {
            // 存储请求url带的信息
            $jContent = json_encode(
                [
                    'page'=> $request->get['page'],
                    'fd'  => $request->fd,
                    'activity'=> $request->get['activity'] ?? '',
                    'admin' => $request->get['admin_id'] ?? '',
                    'order' => $request->get['order'] ?? '',
                ],true);
            $this->redis->hset(config('tw.redis_key.h2'),$request->fd,$jContent);
            return true;
        }
    }

    public function start()
    {
        self::$server->start();
    }


    public function __clone()
    {

    }


}