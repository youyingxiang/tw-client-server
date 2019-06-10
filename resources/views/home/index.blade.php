<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>评分大屏幕</title>
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="{{tw_asset("vendor/tw/home/css/server.css")}}">
</head>
<body id="screen_body" style="background-image: url({{$oData['banner']}})">
<div class="screen">
    <!--标题-->
    <div class="screen_title">
        <img id="screen_title_img" src="{{tw_asset("vendor/tw/home/img/screen_title_all.png")}}" alt="">
    </div>
    <!--LOGO-->
    <div class="screen_logo">
        <p><b>新天维</b>评分系统</p>
    </div>
    <!--选手-->
    <div class="screen_player">
        <h1>{{$oData['title']}}</h1>
        <img id="screen_player_img" src="{{$player['img']}}" alt="{{$player['name']}}">
        <p><b>当前选手:</b><span id="screen_player_name">{{$player['name']}}</span></p>
    </div>
    <!--评委-->
    <div class="screen_judge">
        <ul>
            @foreach($judges as $vo)
            <li>
                <div class="screen_judge_pw">
                    <img src="{{$vo['img']}}" alt="">
                    <p>0.00</p>
                </div>
                <p class="pw_name">{{$vo['name']}}</p>
            </li>
            @endforeach
        </ul>
    </div>
</div>

<!--弹出层-->
<div class="screen_popup" id="popup">
    <div class="screen_popup_sc">
        <p>99.99</p>
    </div>
</div>
<script src="{{tw_asset('/vendor/tw/global/jQuery/jquery-2.2.3.min.js')}}"></script>
<script>
    <?php
    $key = '^manks.top&swoole$';
    $uid = 100;
    $token = md5(md5($uid) . $key);
    ?>

    function popup() {
        var obj = document.getElementById("popup");
        obj.style.display ="block";
    }
    if (1==2){
        popup();
    }
    /**
     * ws推送
     */
    var ws;//websocket实例
    var lockReconnect = false;//避免重复连接
    var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?page=home&activity={{$oData['id']}}&uid=<?php echo $uid; ?>&token=<?php echo $token; ?>';

    function createWebSocket(url) {
        try {
            ws = new WebSocket(url);
            initEventHandle();
        } catch (e) {
            reconnect(url);
        }
    }

    function initEventHandle() {
        ws.onclose = function () {
            reconnect(wsUrl);
        };
        ws.onerror = function () {
            reconnect(wsUrl);
        };
        ws.onopen = function () {
            //心跳检测重置
            heartCheck.reset().start();
        };
        ws.onmessage = function (event) {
            //如果获取到消息，心跳检测重置
            //拿到任何消息都说明当前连接是正常的
            var data = JSON.parse(event.data);
            $('#screen_player_img').attr('src',data.img);
            $('#screen_player_name').html(data.name);
            heartCheck.reset().start();
        }
    }
    function reconnect(url) {
        if(lockReconnect) return;
        lockReconnect = true;
        //没连接上会一直重连，设置延迟避免请求过多
        setTimeout(function () {
            createWebSocket(url);
            lockReconnect = false;
        }, 2000);
    }
    //心跳检测
    var heartCheck = {
        timeout: 30000,//60秒
        timeoutObj: null,
        serverTimeoutObj: null,
        reset: function(){
            clearTimeout(this.timeoutObj);
            clearTimeout(this.serverTimeoutObj);
            return this;
        },
        start: function(){
            var self = this;
            this.timeoutObj = setTimeout(function(){
                //这里发送一个心跳，后端收到后，返回一个心跳消息，
                //onmessage拿到返回的心跳就说明连接正常
                ws.send("心跳检测消息");
                self.serverTimeoutObj = setTimeout(function(){//如果超过一定时间还没重置，说明后端主动断开了
                    ws.close();//如果onclose会执行reconnect，我们执行ws.close()就行了.如果直接执行reconnect 会触发onclose导致重连两次
                }, self.timeout);
            }, this.timeout);
        },
        header:function(url) {
            window.location.href=url
        }

    }
    createWebSocket(wsUrl);
</script>

</body>
</html>