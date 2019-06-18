@extends('tw::layout.base',['header' => "控制台",'pageTitle'=>'控制台',"pageBtnName"=>'控制台'])
@section('content')
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/css/my_style.css") }}?version=1.0.1">
    {{--样式--}}


    <section class="content">
        <div class="control_box">
            <ul>
                <a id="jump_screen" href="javascript:void(0)">
                    <li>跳转大屏幕</li>
                </a>
                <a id="jump_rank" href="javascript:void(0)">
                    <li>跳转排名页</li>
                </a>
                <a id="next_player" href="javascript:void(0)">
                    <li>推送下一个选手</li>
                </a>
                <a href="{{tw_route('tw.player.index',['activity_id'=>(int)request('id')])}}">
                    <li>选手列表</li>
                </a>
            </ul>
        </div>
    </section>
    <script type="text/javascript">
        $(function(){
            // 跳转大屏幕
            $("#jump_screen").on('click',function () {
                var json = '{"type":"3","activity":"{{request('id')}}"}';
                pushSwoole(json);
                $.amaran({'message':'跳转成功'});
            })
            // 跳转选手排行
            $("#jump_rank").on('click',function () {
                var json = '{"type":"4","activity":"{{request('id')}}"}';
                pushSwoole(json);
                $.amaran({'message':'跳转成功'});
            })
            // 点击下一位选手
            $("#next_player").on('click',function () {
                var url = "{{route('tw.player.nextPlayer',request('id'))}}";
                $.ajax({
                    url: url,
                    type:'get',
                    dataType: "json",
                    error:function(data){
                        $.amaran({'message':"服务器繁忙, 请联系管理员！"});
                        return;
                    },
                    success:function(result){
                        if(result.status == 1){
                            var json  = '{"type":"1","player":"'+result.info+'"}';
                            pushSwoole(json);
                            $.amaran({'message':"推送成功"});
                        } else {
                            $.amaran({'message':result.info});
                        }
                    },
                })
            })

            function pushSwoole(json)
            {
                var wsUrl = "ws://{{$_SERVER['HTTP_HOST']}}:9502?page=jump&token={{hash_make(['jump'])}}";
                var ws = new WebSocket(wsUrl);
                ws.onopen= function (event) {
                    ws.send(json);
                }
            }
        });
    </script>
@endsection
