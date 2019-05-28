<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/28
 * Time: 4:13 PM
 */
namespace Tw\Server\Console;
use Illuminate\Console\Command;
class Swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tw:swoole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "this is websocket push client";

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

    }
    public static function getWebSocketServer()
    {
        if (!(self::$server instanceof \swoole_websocket_server)) {
            self::setWebSocketServer();
        }
        return self::$server;
    }

    public function pushMessage()
    {
        foreach (self::$server->connections as $fd) {
            if (self::$server->isEstablished($fd)) {
                self::$server->push($fd, json_encode(['name'=>'游兴祥','age'=>23]));
            }
        }
    }

    public function __clone()
    {

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = self::getWebSocketServer();
        $server->on('open', function ($server, $request) {
            var_dump($request->fd, $request->get, $request->server);
            $server->push($request->fd, "hello, welcome\n");
        });

        //监听WebSocket消息事件
        $server->on('message', function ($server, $frame) {
            echo "Message: {$frame->data}\n";
            $server->push($frame->fd, "server: this is server message");
        });
        //监听WebSocket连接关闭事件
        $server->on('close', function ($server, $fd) {
            echo "client-{$fd} is closed\n";
        });

        $server->on('request', function ($request, $response) {
            call_user_func([__CLASS__,'pushMessage']);
        });

        $this->line("服务正式启动......");
        $server->start();

    }
}