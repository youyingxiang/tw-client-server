@if(!empty(request()->input('activity_id')) && !empty($aData[0]->activity->title))
    @php $pageTitle = $aData[0]->activity->title.'-评委' @endphp
@endif
@extends('tw::layout.base',['header' => "活动管理",'pageTitle'=>$pageTitle??'评委',"pageBtnName"=>'活动列表'])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border screen-box">
                        <h3 class="box-title"></h3>
                        <div class="pull-left">
                            {!! button(route('tw.judges.create'),'create') !!}
                            {!! button(route('tw.judges.destroy','all'),'delete_all') !!}
                        </div>
                        @include('tw::layout.search')
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover table-sort">
                            <tr>
                                <th width="35"><input type="checkbox" class="minimal checkbox-toggle"></th>
                                <th>序号</th>
                                <th>评委名称</th>
                                <th>图像</th>
                                @if(empty(request()->input('activity_id')))
                                <th>所属活动</th>
                                @endif
                                <th>评委二维码</th>
                                <th>连接状态</th>
                                <th>添加日期</th>
                                <th>操作</th>
                            </tr>
                            @foreach($aData as $vo)
                                <tr>
                                    <td style="vertical-align:middle"><input type="checkbox" name="id[]" value="{{$vo['hid']}}" class="minimal"></td>
                                    <td style="vertical-align:middle">{{item_no($loop->iteration)}}</td>
                                    <td style="vertical-align:middle"><span class="editable" data-pk="{{$vo['id']}}" data-name="name" data-url="{{tw_route('tw.judges.update',$vo['id'])}}" >{{$vo['name']}}</span></td>
                                    <td style="vertical-align:middle"><img src="{{$vo['img']}}" style="width:40px;border-radius:40%;" /></td>
                                    @if(empty(request()->input('activity_id')))
                                        <td style="vertical-align:middle">{{$vo->activity->title}}</td>
                                    @endif
                                    <td style="vertical-align:middle">{!! $vo['qr_code'] !!}</td>
                                    <td style="vertical-align:middle">
                                        <a href="javascript:void(0);" data-id="{{$vo['id']}}" class='judges_{{$vo['id']}} linkstate editimg fa @if($vo['link_state'] == 1)fa-check-circle text-green @else fa-times-circle text-red @endif'>
                                        </a>
                                    </td>
                                    <td style="vertical-align:middle">{{$vo['created_at']}}</td>
                                    <td style="vertical-align:middle">
                                        {!! button(tw_route('tw.judges.edit',$vo['id']),'edit') !!}
                                        {!! button(tw_route('tw.judges.destroy',$vo['id']),'delete',$vo['id']) !!}
                                        {!! button(tw_route('tw.judges.clearlink',$vo['id']),'clearLink',$vo['id']) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        {{ $aData->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(function () {
            $(".linkstate").on('click',function () {
                return false;
            })
            var activity_id = "{{request()->input('activity_id')??''}}";

            var ws;//websocket实例
            var lockReconnect = false;//避免重复连接
            var wsUrl = 'ws://{{$_SERVER["HTTP_HOST"]}}:9502?page=adminjudges&activity={{request()->input('activity_id')??''}}&token={{hash_make(['adminjudges',request()->input('activity_id')])}}';

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
                    if (data.linkstate == 1) {
                        $(".judges_"+data.judges_id+"").removeClass('fa-times-circle text-red').addClass('fa-check-circle text-green');
                    } else if (data.onlinkcode == 200) {
                        $(".linkstate").each(function () {
                            if ($.inArray($(this).attr('data-id'),data.online_judges) >= 0) {
                                $(this).removeClass('fa-times-circle text-red').addClass('fa-check-circle text-green');
                            } else {
                                $(this).removeClass('fa-check-circle text-green').addClass('fa-times-circle text-red');
                            }
                        })
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
                        ws.send('{"type":"5","activity_id":"'+activity_id+'"}');
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
@endsection
