<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <title>评委评分</title>
    <link rel="stylesheet" href="{{tw_asset("/vendor/tw/home/css/style.css")}}?version=1.0.1">

</head>
<body>
<h1 style="margin: 0; margin-top: 1rem;font-size: 1.5rem">请为选手<br><b data-id="{{$aPlayer['id'] ?? ''}}"  id="screen_player_name">{{$aPlayer['name'] ?? ''}}</b> 评分</h1>
<div id="calculator" class="calculator" style="font-size: 1.5em">
    <p style="font-size: 0.5em;text-align: center">当前评委：<b style="font-size: 1.5em">{{$oJudges['name']}}</b></p>
    <div id="viewer" class="viewer">0</div>
    <button class="num" data-num="1">1</button>
    <button class="num" data-num="2">2</button>
    <button class="num" data-num="3">3</button>
    <button class="num" data-num="4">4</button>
    <button class="num" data-num="5">5</button>
    <button class="num" data-num="6">6</button>
    <button class="num" data-num="7">7</button>
    <button class="num" data-num="8">8</button>
    <button class="num" data-num="9">9</button>
    <button class="num" data-num="0">0</button>
    <button class="num" data-num=".">.</button>
    <button id="clear" class="clear">&larr;</button>
    <button style="width: 11em;margin: 0 auto;float: none;" id="equals" class="equals"  data-result="">提交</button>
</div>
<p class="warning">最高分为100分，保留小数点2位。</p>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/home/js/calculator.js')}}" type="text/javascript"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/global/jQuery/jquery-2.2.3.min.js')}}"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/home/js/public.js')}}"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/system/layer/layui.js')}}"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/system/layer/layer.js')}}"></script>
<script>

    $("#equals").on('click',function() {
        var obj = $("#viewer");
        inputnum(obj,$("#viewer").html().trim());
        var score = $("#viewer").html().trim();
        var playerid = $("#screen_player_name").attr('data-id').trim();
        if (/^\d+$/.test(score) == false && /^\d+\.\d{0,2}$/.test(score) == false) {
            showErrMsgTime('请输入0-100有效分数！',2);
            $("#viewer").html(0);
        } else if (score > 100) {
            showErrMsgTime('请输入0-100有效分数！',2);
            $("#viewer").html(0);
        } else if (!playerid) {
            showErrMsgTime('当前没有推送的选手！',2);
            $("#viewer").html(0);
        } else {
            $.ajax({
                url: "{{route('tw.home.postScoring')}}",
                type:'post',
                dataType: "json",
                data:{activity_id:"{{$sActivityId ??''}}",player_id:playerid,score:score,'judges_id':"{{request('judgesId')}}"},
                error:function(data){
                    showErrMsgTime("服务器繁忙, 请联系管理员！",3);
                    return;
                },
                success:function(result){
                    if(result.status == 1){
                        pushSwoole(playerid);
                        showOkTime("评分成功！",3)
                    } else {
                        showErrMsgTime(result.info,3);
                    }
                },
            })
        }
    });

    $(".num").on("click",function () {
        inputnum($("#viewer"),$("#viewer").html().trim());
    })
    function inputnum(obj,val){
        obj.empty().html(val.replace(/[^\d.]/g,"")); //清除"数字"和"."以外的字符
        obj.empty().html(val.replace(/^\./g,"")); //验证第一个字符是数字
        obj.empty().html(val.replace(/\.{2,}/g,"")); //只保留第一个, 清除多余的
        obj.empty().html(val.replace(".","$#$").replace(/\./g,"").replace("$#$","."));
        obj.empty().html(val.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3')); //只能输入两个小数
    }




    /**
     * ws推送
     */
    var ws;//websocket实例
    var lockReconnect = false;//避免重复连接
    var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?stoken={{session()->getId()}}&page=judges&activity={{$sActivityId ??''}}&judges_id={{$oJudges["id"]}}&token={{hash_make([session()->getId(),'judges',$sActivityId ??'',$oJudges["id"]])}}';



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
               // $('#screen_player_img').attr('src', data.player.img);
                $('#screen_player_name').html(data.player.name);
                $("#screen_player_name").attr('data-id', data.player.id);
                $("#viewer").html(0);
            } else if (data.url) {
                window.location.href=data.url;
            }


            heartCheck.reset().start();
        }
    }
    createWebSocket(wsUrl);
</script>
</body>
</html>