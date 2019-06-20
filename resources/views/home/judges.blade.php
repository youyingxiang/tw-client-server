<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <title>评委评分</title>
    <link rel="stylesheet" href="{{tw_asset("/vendor/tw/home/css/server.css")}}?version=1.0.1">
</head>
<body>
<div class="judge_main">
    <div class="judge_top">
        <img src="{{tw_asset("/vendor/tw/home/img/screen_bg.jpg")}}" alt="">
    </div>
    <div class="judge_player">
        <div class="judge_tx">
            <img id="screen_player_img" data-id="{{$aPlayer['id'] ?? ''}}" src="{{$aPlayer['img'] ?? ''}}" alt="">
        </div>
        <p>当前选手：<b id="screen_player_name" >{{$aPlayer['name'] ?? ''}}</b></p>
    </div>
    <div class="judge_fg"></div>
    <div class="judge_conter">
        <div class="judge_from">
            <h3>姓名：{{$oJudges['name']}}</h3>
            <p>请输入分数</p>
            <input type="text" id="score" oninput="inputnum(this)" placeholder="最多保留两位小数" >
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
<script src="{{tw_asset('/vendor/tw/home/js/wbsocket.js')}}"></script>
<script>

    $("#input_sub").on('click',function() {
        var score = $("#score").val();
        var playerid = $("#screen_player_img").attr('data-id').trim();
        if (/^\d+$/.test(score) == false && /^\d+\.\d{0,2}$/.test(score) == false) {
            alert('请输入0-100有效分数！');
            $("#score").val("");
        } else if (score > 100) {
            alert('请输入0-100有效分数！');
            $("#score").val("");
        } else if (!playerid) {
            alert('当前没有推送的选手！');
            $("#score").val("");
        } else {
            $.ajax({
                url: "{{route('tw.home.postScoring')}}",
                type:'post',
                dataType: "json",
                data:{activity_id:"{{$sActivityId ??''}}",player_id:playerid,score:score,'judges_id':"{{request('judgesId')}}"},
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
                },
            })
        }
    });
    function inputnum(obj,val){
        obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
        obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
        obj.value = obj.value.replace(/\.{2,}/g,""); //只保留第一个, 清除多余的
        obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
        obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
    }




    /**
     * ws推送
     */
    var ws;//websocket实例
    var lockReconnect = false;//避免重复连接
    var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?page=judges&activity={{$sActivityId ??''}}&token={{hash_make(['judges',$sActivityId ??''])}}';



    function pushSwoole(playerid)
    {
        var wsUrl = "ws://{{$_SERVER['HTTP_HOST']}}:9502?page=judges&token={{hash_make(['judges'])}}";
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
            if (data.player) {
                $('#screen_player_img').attr('src', data.player.img);
                $('#screen_player_name').html(data.player.name);
                $("#screen_player_img").attr('data-id', data.player.id);
                $("#score").val("");
            }

            heartCheck.reset().start();
        }
    }
    createWebSocket(wsUrl);
</script>
</html>