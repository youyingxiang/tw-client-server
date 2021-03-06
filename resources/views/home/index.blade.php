<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>评分大屏幕</title>
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="{{tw_asset("/vendor/tw/home/css/server.css")}}?version=1.0.1">
    <link rel="stylesheet" href="{{tw_asset("/vendor/tw/system/layer/skin/default/layer.css")}}?version=1.0.1">
</head>
<body id="screen_body" style="background-image: url({{$oData['banner']}})">
<div class="screen">
    <!--标题-->
    <div class="screen_title">
        <img id="screen_title_img" src="{{tw_asset("/vendor/tw/home/img/screen_title_all.png")}}" alt="">
    </div>
    <!--LOGO-->
    @if($oData['level'] == 1)
        <div class="screen_logo">
            <p><b>天维</b>评分系统@if($oData['release_state'] == 0)（活动暂未发布）@endif</p>
        </div>
@endif
<!--选手-->
    <div class="screen_player">
        <h1>{{$oData['title'] ?? ''}}</h1>
        <img id="screen_player_img" src="{{$player['img']??''}}" alt="{{$player['name']??''}}">
        <p><b>当前选手:</b><span id="screen_player_name">{{$player['name']??''}}</span></p>
    </div>
    <!--评委-->
    <div class="screen_judge">
        <ul>
            @foreach($judges as $vo)
                <li class="judge_css" @if(count($judges) > 7)style="width: 8%;"@endif >
                    <div class="screen_judge_pw">
                        <img id="judge{{$vo['id']}}" src="{{$vo['img']}}" alt="">
                        <p class="score_res">{{$hScore[$vo['id']] ?? "0.00"}}</p>
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
<script type="text/javascript" src="{{tw_asset('/vendor/tw/global/jQuery/jquery-2.2.3.min.js')}}"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/home/js/public.js')}}"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/system/layer/layui.js')}}"></script>
<script type="text/javascript" src="{{tw_asset('/vendor/tw/system/layer/layer.js')}}"></script>
<script>
    var judgesNuml = "{{count($judges)}}";


    function popup() {
        var obj = document.getElementById("popup");
        obj.style.display = "block";
    }

    if (1 == 2) {
        popup();
    }
    /**
     * ws推送
     */
    var ws;//websocket实例
    var lockReconnect = false;//避免重复连接
    var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?page=home&activity={{$oData['id']}}&token={{hash_make(['home',$oData['id']])}}';


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
            // 评委打分
            if (data.player && !jQuery.isEmptyObject(data.judges_score)) {

                $('#screen_player_img').attr('src', data.player.img);
                $('#screen_player_name').html(data.player.name);
                $('.score_res').each(function () {
                    $(this).empty().html('0.00');
                })
                $.each(data.judges_score, function (key, value) {
                    $("#judge" + key + "").next().html(value);
                })
            } else if (!data.player && data.judges_score) {
                $.each(data.judges_score, function (key, value) {
                    $("#judge" + key + "").next().html(value);
                })
            } else if (data.player && jQuery.isEmptyObject(data.judges_score)) {
                $('#screen_player_img').attr('src', data.player.img);
                $('#screen_player_name').html(data.player.name);
                $('.score_res').each(function () {
                    $(this).empty().html('0.00');
                })
            } else if (data.url) {
                window.location.href = data.url;
            } else if (data.state == 1 && data.score > 0) {
                showOkTime(data.info + " 最后得分 " + data.score, 3);
            }
            heartCheck.reset().start();
        }
    }

    createWebSocket(wsUrl);
</script>

</body>
</html>