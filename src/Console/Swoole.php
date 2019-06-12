<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 4:13 PM
 */
namespace Tw\Server\Console;
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

    protected $redis;

    // websocket服务
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
        /**
         * @see
         * type = 1 推送选手到大屏幕
         */
        if ( isset($aData['type']) ) {
            $this->pushMessage($aData);
        }
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request,$response)
    {
        $this->pushMessage();
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
     * 推送请求消息处理
     */
    public function pushMessage(array $aData)
    {
        if (isset($aData['player']))
            $aResult = DB::table('tw_player')->find($aData['player']);
        foreach (self::$server->connections as $fd) {
            if (self::$server->isEstablished($fd)) {
                $jContent = json_decode($this->redis->hget(config('tw.redis_key.h2'),$fd),true);
                if ($jContent && ($jContent['page'] == "home" || $jContent['page'] =="judges" )&& $aData['type'] == "1") {
                    if ($aResult->activity_id == $jContent['activity'])  // 根据推送返回Id 查询选手信息 找到属于活动 只推送给对应活动
                        self::$server->push($fd, json_encode($aResult,JSON_UNESCAPED_UNICODE));
                } else if ($jContent && $jContent['page'] == "home" && $aData['type'] == 2) {
                    if ($aResult->activity_id == $jContent['activity']) {
                        $hData['judges_score'] = Redis::hgetall(config('tw.redis_key.h1').$aData['player']);
                        self::$server->push($fd, json_encode($hData,JSON_UNESCAPED_UNICODE));
                    }
                }
            } else {
                $this->redis->hdel(config('tw.redis_key.h2'),$fd);
            }
        }
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
                    'activity'=> $request->get['activity'] ?? ''
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