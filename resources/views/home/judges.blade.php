<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <title>评委评分</title>
    <link rel="stylesheet" href="{{tw_asset("/vendor/tw/home/css/server.css")}}">
</head>
<body>
<div class="judge_main">
    <div class="judge_top">
        <img src="{{tw_asset("/vendor/tw/home/img/screen_bg.jpg")}}" alt="">
    </div>
    <div class="judge_player">
        <div class="judge_tx">
            <img id="screen_player_img" data-id="{{$aPlayer['id']}}" src="{{$aPlayer['img']}}" alt="">
        </div>
        <p>当前选手：<b id="screen_player_name" >{{$aPlayer['name']}}</b></p>
    </div>
    <div class="judge_fg"></div>
    <div class="judge_conter">
        <div class="judge_from">
            <p>请输入分数</p>
            <input type="text" id="score" placeholder="最多保留两位小数" >
            <input type="submit" value="提交" id="input_sub">
            <h3>温馨提示</h3>
            <p id="input_sub_p">如出现问题请及时联系售后客服 ：0736-8888888</p>
        </div>
    </div>
    <div class="judge_footer">
        <img src="{{tw_asset("/vendor/tw/home/img/footer.png")}}" alt="">
    </div>
</div>
</body>
<script src="{{tw_asset('/vendor/tw/global/jQuery/jquery-2.2.3.min.js')}}"></script>
<script>
    <?php
    $key = '^manks.top&swoole$';
    $uid = 100;
    $token = md5(md5($uid) . $key);
    ?>


    $("#input_sub").on('click',function(){
        var score = $("#score").val();
        var playerid = $("#screen_player_img").attr('data-id').trim();
        if (/^\d+$/.test(score) == false && /^\d+\.\d{0,2}$/.test(score) == false) {
            alert('你输入的不是有效分数！');
            $("#score").val("");
        } else {
            $.ajax({
                url: "{{route('tw.home.postScoring')}}",
                type:'post',
                dataType: "json",
                data:{activity_id:"{{$aPlayer['activity_id']}}",player_id:playerid,score:score,'judges_id':"{{request('judgesId')}}"},
                error:function(data){
                    alert("服务器繁忙, 请联系管理员！");
                    return;
                },
                success:function(result){
                    if(result.status == 1){
                        pushSwoole(playerid);
                        alert("评分成功！")
                    } else {
                        alert(result.info);
                    }
                    $("#score").val("");
                },
            })
        }
    });




    /**
     * ws推送
     */
    var ws;//websocket实例
    var lockReconnect = false;//避免重复连接
    var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?page=judges&activity={{$aPlayer['activity_id']}}&uid=<?php echo $uid; ?>&token=<?php echo $token; ?>';

    function createWebSocket(url) {
        try {
            ws = new WebSocket(url);
            initEventHandle();
        } catch (e) {
            reconnect(url);
        }
    }

    function pushSwoole(playerid)
    {
        var wsUrl = "ws://{{$_SERVER['HTTP_HOST']}}:9502?page=judges&uid=100&token={{$token}}";
        var ws = new WebSocket(wsUrl);
        ws.onopen= function (event) {
            //ws.send('{"type":"1","player":"'+id+'"}');
            ws.send('{"type":"2","player":"'+playerid+'"}')
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

            $('#screen_player_img').attr('src', data.img);
            $('#screen_player_name').html(data.name);
            $("#screen_player_img").attr('data-id', data.id);

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
        timeout: 60000,//60秒
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
</html>