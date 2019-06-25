@extends('tw::layout.base',['header' => "活动",'pageTitle'=>'活动续费',"pageBtnName"=>'扫码支付'])
@section('content')
    <style>
        svg{
            display: block;
            margin: 0 auto;
        }
        .content-wrapper{
            background: #fff !important;
        }
        .col-md-6{
            background: #1dab39 !important;
            text-align: center;
            width: 30%;
            margin: 0 auto;
            float: none;
            padding: 30px 0;
            margin-top: 50px;
        }
        .col-md-6 h3{
            line-height: 45px !important;
            color: #fff !important;
        }
        svg{
            width: 90% !important;
            height: 50% !important;
        }
    </style>
    <section class="content">
        <div class="row">

            <div class="col-md-12">

            </div>

        </div>
        <div class="row">

            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <h3>使用微信扫码支付</h3>
             {!! $oData['qrcode'] !!}
                <h3 class="text-center">付款金额：{{$oData['pay_amount']}}</h3>
            </div>
            <div class="col-md-3">
            </div>
            {{--<div class="col-md-3"><a id="pay_res" class="btn btn-default"> 查看支付结果</a></div>--}}
        </div>
    </section>
    @if(request('order_no'))
    <script type="text/javascript">
        $(function() {

            $("#pay_res").on('click',function () {
                var url = "{{route('tw.payorder.check',request('order_no'))}}";
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
                            $.amaran({'message':result.info});
                            $.pjax({url: result.url, container: '#pjax-container', fragment:'#pjax-container'})
                        } else {
                            $.amaran({'message':result.info});
                        }
                    },
                })
            })
            var ws;//websocket实例
            var lockReconnect = false;//避免重复连接
            var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?page=qrcode&admin_id={{adminId()}}&order={{request('order_no')}}&token={{hash_make(['qrcode',adminId(),request('order_no')])}}';

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
                    console.log(data);
                    if (data.state == 1) {
                        $.amaran({'message':data.info});
                        $.pjax({url: data.url, container: '#pjax-container', fragment:'#pjax-container'})
                    }
                    heartCheck.reset().start();
                }
            }
            createWebSocket(wsUrl);

            /**
             * 创建链接
             * @param url
             */
            function createWebSocket(url) {
                try {
                    ws = new WebSocket(url);
                    initEventHandle();
                } catch (e) {
                    reconnect(url);
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
                        ws.send("heartbeat");
                        self.serverTimeoutObj = setTimeout(function(){//如果超过一定时间还没重置，说明后端主动断开了
                            ws.close();//如果onclose会执行reconnect，我们执行ws.close()就行了.如果直接执行reconnect 会触发onclose导致重连两次
                        }, self.timeout);
                    }, this.timeout);
                },
                header:function(url) {
                    window.location.href=url
                }

            }


        })
    </script>
    @endif
@endsection