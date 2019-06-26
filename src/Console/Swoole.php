<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 4:13 PM
 */
namespace Tw\Server\Console;
use Closure;
use Tw\Server\Facades\Tw;
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
        $this->redis->del(config('tw.redis_key.h2'));
        $this->redis->del(config('tw.redis_key.hset1'));
        $this->redis->del(config('tw.redis_key.h4'));
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
        $this->checkAccess($server, $request);
    }
    /**
     * @param $serv
     * @param $frame
     */
    public function onMessage($server, $frame)
    {
        $aData = json_decode($frame->data,true);
        $aData['fd'] = $frame->fd ?? '';
        if (isset($aData['type'])) {
            $sFuncName = $this->enum((int)$aData['type']);
            if ($sFuncName && method_exists($this,$sFuncName)) {
                call_user_func([$this,$sFuncName],$aData);
            }
        }
    }

    /**
     * @param $request
     * @see 客户端评委扫码进入的时候 检测是否登陆过
     */
    public function openCheckJudgesLogin($request)
    {
        if (
            isset($request->get['page'])
            && $request->get['page'] == "judges"
            && isset($request->get['judges_id'])
        ) {
           $oJudges =  Tw::newModel("Judges")->find($request->get['judges_id']);
            if ($oJudges->link_state == 1 && $request->get['stoken'] != $oJudges->session_id)
            {
                $aRes['url'] = tw_route('tw.home',(int)$request->get['activity']);
                self::$server->push($request->fd,xss_json($aRes));
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
            4 => "jumpHome",
            5 => "heartBeatLoginDynamic"
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
            $aJudgesScores = Redis::hgetall(config('tw.redis_key.h1').$aData['player']);
            $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($aData,$aResult,$aJudgesScores) {
                // 推送首页和评委页面
                if ($aContent && ($aContent['page'] == "home" || $aContent['page'] =="judges") ) {
                    if ($aResult->activity_id == $aContent['activity']) {
                        $aRes['judges_score'] = $aJudgesScores;
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
     * @see 心跳检测检测评委登陆状态
     */
    public function heartBeatLoginDynamic(array $aData)
    {
        $aRes['onlinkcode'] = 200;
        foreach (self::$server->connections as $fd) {
            if (self::$server->isEstablished($fd)) {
                $aContent = json_decode($this->redis->hget(config('tw.redis_key.h2'),$fd),true);
                if ($aContent && ($aContent['page'] == "judges") ) {
                    if ($aData['activity_id'] == $aContent['activity']) {
                        $aRes['online_judges'][] = $aContent['judges'];
                    }
                }
            } else {
                $this->redis->hdel(config('tw.redis_key.h2'),$fd);
            }
        }
        $aRes['online_judges'] = $aRes['online_judges']??[];
        if ($aRes['online_judges'])
            DB::table('tw_judges')->whereNotIn('id',$aRes['online_judges'])->update(["link_state"=>0]);
        else
            DB::table('tw_judges')->where('activity_id',$aData['activity_id'])->update(["link_state"=>0]);
        self::$server->push($aData['fd'],xss_json($aRes));
    }

    /**
     * @param array $aData
     * @see 评委打分逻辑
     */
    public function judgesScore(array $aData)
    {
        $aResult = DB::table('tw_player')->find($aData['player']);
        if ($aResult) {
            $aJudgesScores = Redis::hgetall(config('tw.redis_key.h1').$aData['player']);
            $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($aData,$aResult,$aJudgesScores) {
                if ($aContent && $aContent['page'] == "home") {
                    if ($aResult->activity_id == $aContent['activity']) {
                        $hData['judges_score'] = $aJudgesScores;
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
     * @see 清除评委连接 在线评委跳转首页
     */
    public function clearJudgesLink($request)
    {
        $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($request) {
            if ($aContent && $aContent['page'] == "judges") {
                if (
                    isset($request->get['activity_id'])
                    && $request->get['activity_id'] == $aContent['activity']
                    && isset($request->get['judges_id'])
                    && $request->get['judges_id'] == $aContent['judges']
                ) {
                    $aRes['url'] = route("tw.home.judgeslinkerr",1);
                    $oSwoole::$server->push($fd,xss_json($aRes));
                }
            }
        };
        $this->eachFdLogic($callback);
    }

    /**
     * @param $request
     * @see 评委扫码登陆状态
     */
    public function judgesLoginDynamic($request)
    {
        $callback = function (array $aContent,int $fd,Swoole $oSwoole)use($request) {
            if ($aContent && $aContent['page'] == "adminjudges") {
                if (
                    isset($request->get['activity_id'])
                    && $request->get['activity_id'] == $aContent['activity']
                ) {
                    $aRes['linkstate'] = $request->get['linkstate'] ?? 0;
                    $aRes['judges_id'] = $request->get['judges_id'] ?? 0;
                    $oSwoole::$server->push($fd,xss_json($aRes));
                }
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
    public function onClose($serv,$fd)
    {

        $this->beforeClose($fd);
        $this->line("客户端 {$fd} 关闭");
    }

    /**
     * @param $fd
     */
    public function beforeClose($fd)
    {
        if ($this->redis->exists(config('tw.redis_key.h2'))) {
            $aData = $this->redis->hget(config('tw.redis_key.h2'),$fd);
            $aData = !empty($aData) ? json_decode($aData,true) : [];
            if (
                isset($aData['page'])
                && $aData['page'] == "judges"
                && isset($aData['judges'])
            ) {
                // 清除评委登陆信息
                $this->redis->srem(config('tw.redis_key.hset1'),$aData['judges']);
                $this->redis->hdel(config('tw.redis_key.h4'),$aData['judges']);
                $request = (object)null;
                $request->get['linkstate'] = 0;
                $request->get['judges_id'] = $aData['judges'];
                $request->get['activity_id'] = $aData['activity'];
                $this->judgesLoginDynamic($request);
            }
            // 删除链接
            $this->redis->hdel(config('tw.redis_key.h2'), $fd);
        }
    }


    /**
     * 校验客户端连接的合法性,无效的连接不允许连接
     * @param $serv
     * @param $request
     * @return mixed
     */
    public function checkAccess($server, $request):bool
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
            $bRes = $this->storeUrlParamToReis($request);
            return $bRes;
        }
    }




    /**
     * @param $request
     * @see 存储请求参数url信息
     */
    public function storeUrlParamToReis($request):bool
    {
        $bRes = true;
        // 存储请求url带的信息
        $jContent = json_encode(
            [
                'page'=> $request->get['page'],
                'fd'  => $request->fd,
                'activity'=> $request->get['activity'] ?? '',
                'admin' => $request->get['admin_id'] ?? '',
                'order' => $request->get['order'] ?? '',
                'judges' => $request->get['judges_id'] ?? ''
            ],true);
        $this->redis->hset(config('tw.redis_key.h2'),$request->fd,$jContent);
        if (
            isset($request->get['page'])
            && $request->get['page'] == "judges"
            && isset($request->get['judges_id'])
            && isset($request->get['stoken'])
        ) {
            $bRes = $this->setJudgesRedisByJudgesUnique($request->get['judges_id'],$request->get['stoken']);
            if (!$bRes)
                $this->judgesLoginFail($request);
            else
                $this->judgesLoginSuccess($request);
        }
        return $bRes;
    }



    /**
     * @see 评分界面 只能登陆一次
     * 通过redis集合维护评委登陆信息
     */
    public function setJudgesRedisByJudgesUnique(string $sJudgesId,string $sSessionId):bool
    {
        $bRes = $this->redis->sadd(config('tw.redis_key.hset1'),$sJudgesId);
        $sJudgesSessionId = $this->redis->hget(config('tw.redis_key.h4'),$sJudgesId);
        if ($bRes) {
            $this->redis->hset(config('tw.redis_key.h4'),$sJudgesId,$sSessionId);
        } else {
            if ($sSessionId == $sJudgesSessionId)
                $bRes = true;
        }

        return $bRes;
    }

    /**
     * @param $request
     * @see 扫码登陆失败
     */
    public function judgesLoginFail($request)
    {
        $this->line("此评委已在登陆状态！");
        $aRes['url'] = route("tw.home.judgeslinkerr",2);
        self::$server->push($request->fd,xss_json($aRes));
        // 防止登陆集合的登陆评委id 被删除
        $this->redis->hdel(config('tw.redis_key.h2'), $request->fd);
        self::$server->close($request->fd);
    }

    /**
     * @param $request
     * @see 扫码登陆成功
     */
    public function judgesLoginSuccess($request)
    {
        $request->get['linkstate'] = 1;
        $request->get['activity_id'] = $request->get['activity'];
        $this->judgesLoginDynamic($request);
    }




    public function start()
    {
        self::$server->start();
    }


    public function __clone()
    {

    }


}