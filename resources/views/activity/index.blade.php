@extends('tw::layout.base',['header' => "活动",'pageTitle'=>'活动',"pageBtnName"=>'活动列表'])
@section('content')
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/css/my_style.css") }}?version=1.0.2">
    {{--样式--}}


    <section class="content">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p>当活动未发布时：评委和选手最多添加2人。</br>
                普通活动发布后：评委最多添加5人，选手最多添加10人。如要添加更多评委和选手请升级高级活动（高级活动不限制选手和评委数量）。
                </br>默认有限时间为：从发布之日起延后三天。如需更长有限时间，请进行天数续费。
            </p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{--切换--}}
                    <div class="tab_act">
                        <ul>
                            <li onclick="act_pt()" id="act_pt" style="background: #3c8dbc;color: #fff">普通活动</li>
                            <li onclick="act_gj()" id="act_gj">高级活动</li>
                            <a id="create_activity" href="{!!route('tw.activity.create')!!}">创建新活动</a>
                        </ul>
                    </div>
                    {{--普通--}}
                    <div class="actvity_pt" id="putong">
                        <ul>
                            @foreach($aData1 as $vo)
                                <li>
                                    <div class="pt_left">
                                        <h4>{{$vo['title']}}</h4>
                                        <p style="color: #fff">期限：{!! $vo['term'] !!}</p>
                                        <div style="margin-bottom: 15px">
                                            <a data-id="{{$vo['id']}}" href="javascript:void(0)"
                                               class="level2">升级高级活动</a>
                                            <a class="adddays" data-id="{{$vo['id']}}"
                                               href="javascript:void(0)">续费天数</a>
                                            <a class="release" data-state="{{$vo['release_state']}}"
                                               data-url="{{tw_route('tw.activity.release',$vo['id'])}}"
                                               href="javascript:void(0)">活动发布</a>
                                        </div>
                                    </div>
                                    <div class="pt_right">

                                        <div class="pt_ico"><a href="{{tw_route('tw.activity.edit',$vo['id'])}}"><i
                                                        class="img_1"></i>
                                                <p>活动设置</p></a></div>

                                        <div class="pt_ico"><a
                                                    href="{{tw_route('tw.judges.index',['activity_id'=>$vo['id']])}}"><i
                                                        class="img_3"></i>
                                                <p>评委设置</p></a></div>
                                        <div class="pt_ico"><a
                                                    href="{{tw_route('tw.player.index',['activity_id'=>$vo['id']])}}"><i
                                                        class="img_4"></i>
                                                <p>选手设置</p></a></div>
                                        <div class="pt_ico">
                                            <a class="jump_screen" data-id="{{$vo['id']}}" href="javascript:void(0)">
                                                <i class="img_5"></i>
                                                <p>跳转大屏幕</p></a></div>
                                        <div class="pt_ico">
                                            <a class="jump_rank" data-id="{{$vo['id']}}" href="javascript:void(0)">
                                                <i class="img_5"></i>
                                                <p>跳转排名</p></a></div>
                                        <div class="pt_ico">
                                            <a class="next_player"
                                               data-url="{{route('tw.player.nextPlayer',$vo['id'])}}"
                                               href="javascript:void(0)">
                                                <i class="img_5"></i>
                                                <p>下一个选手</p></a></div>

                                        <div class="pt_last_b">
                                            <a class="large_screen" data-state="{{$vo['release_state']}}"
                                               target="_blank"
                                               href="{{tw_route('tw.home',$vo['id'])}}">活动大屏幕</a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="box-footer clearfix">
                            {{ $aData1->links() }}
                        </div>
                    </div>

                    {{--高级--}}
                    <div class="actvity_pt" id="gaoji" style="display: none">
                        <ul>
                            @foreach($aData2 as $vo)
                                <li>
                                    <div class="pt_left">
                                        <h4>{{$vo['title']}}</h4>
                                        <p style="color: #fff">期限：{!! $vo['term'] !!}</p>
                                        <a class="adddays" data-id="{{$vo['id']}}" href="javascript:void(0)">续费天数</a>
                                        <a class="release" data-state="{{$vo['release_state']}}"
                                           data-url="{{tw_route('tw.activity.release',$vo['id'])}}"
                                           href="javascript:void(0)">活动发布</a>
                                    </div>
                                    <div class="pt_right">
                                        <div class="pt_ico"><a href="{{tw_route('tw.activity.edit',$vo['id'])}}"><i
                                                        class="img_1"></i>
                                                <p>活动设置</p></a></div>

                                        <div class="pt_ico"><a
                                                    href="{{tw_route('tw.judges.index',['activity_id'=>$vo['id']])}}"><i
                                                        class="img_3"></i>
                                                <p>评委设置</p></a></div>
                                        <div class="pt_ico"><a
                                                    href="{{tw_route('tw.player.index',['activity_id'=>$vo['id']])}}"><i
                                                        class="img_4"></i>
                                                <p>选手设置</p></a></div>
                                        <div class="pt_ico">
                                            <a class="jump_screen" data-id="{{$vo['id']}}" href="javascript:void(0)">
                                                <i class="img_5"></i>
                                                <p>跳转大屏幕</p></a></div>
                                        <div class="pt_ico">
                                            <a class="jump_rank" data-id="{{$vo['id']}}" href="javascript:void(0)">
                                                <i class="img_5"></i>
                                                <p>跳转排名</p></a></div>
                                        <div class="pt_ico">
                                            <a class="next_player"
                                               data-url="{{route('tw.player.nextPlayer',$vo['id'])}}"
                                               href="javascript:void(0)">
                                                <i class="img_5"></i>
                                                <p>下一个选手</p></a></div>

                                        <div class="pt_last_b">
                                            <a class="large_screen"
                                               data-state="{{$vo['release_state']}}" target="_blank"
                                               href="{{tw_route('tw.home',$vo['id'])}}">活动大屏幕</a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="box-footer clearfix">
                            {{ $aData2->links() }}
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{--TAB切换--}}
    <script>

        function act_pt() {
            document.getElementById("act_pt").style.cssText = 'background:#3c8dbc;color:#fff;';
            document.getElementById("act_gj").style.cssText = 'background:none;color:#000;';
            document.getElementById("gaoji").style.cssText = 'display:none';
            document.getElementById("putong").style.cssText = 'display:block';
            $("#create_activity").show();
        }

        function act_gj() {
            document.getElementById("act_gj").style.cssText = 'background:#3c8dbc;color:#fff;';
            document.getElementById("act_pt").style.cssText = 'background:none;color:#000;';
            document.getElementById("gaoji").style.cssText = 'display:block';
            document.getElementById("putong").style.cssText = 'display:none';
            $("#create_activity").hide();
        }

        $(function () {
            // 跳转大屏幕
            $(".jump_screen").on('click', function () {
                var id = $(this).attr('data-id');
                var json = '{"type":"3","activity":' + id + '}';
                pushSwoole(json);
                $.amaran({'message': '跳转成功'});
            })
            // 跳转选手排行
            $(".jump_rank").on('click', function () {
                var id = $(this).attr('data-id');
                var json = '{"type":"4","activity":' + id + '}';
                pushSwoole(json);
                $.amaran({'message': '跳转成功'});
            })


            // 点击下一位选手
            $(".next_player").on('click', function () {
                var url = $(this).attr('data-url');
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: "json",
                    error: function (data) {
                        $.amaran({'message': "服务器繁忙, 请联系管理员！"});
                        return;
                    },
                    success: function (result) {
                        if (result.status == 1) {
                            var json = '{"type":"1","player":"' + result.info + '"}';
                            pushSwoole(json);
                            $.amaran({'message': "推送成功"});
                        } else {
                            $.amaran({'message': result.info});
                        }
                    },
                })
            })

            function pushSwoole(json) {
                var wsUrl = "ws://{{$_SERVER['HTTP_HOST']}}:9502?page=jump&token={{hash_make(['jump'])}}";
                var ws = new WebSocket(wsUrl);
                ws.onopen = function (event) {
                    ws.send(json);
                }
            }

            function dialog(title, message, func) {
                BootstrapDialog.confirm({
                    onshow: function (obj) {
                        var cssConf = {};
                        cssConf['width'] = 300;
                        if (cssConf) {
                            obj.getModal().find('div.modal-dialog').css(cssConf);
                        }
                    },
                    title: title,
                    message: message,
                    btnCancelLabel: '取消',
                    btnOKLabel: '确定',
                    callback: func
                });
            }

            $(".level2").on('click', function () {
                var id = $(this).attr('data-id');
                dialog("活动升级", "确认升级高级活动？", function (resultDel) {
                    if (resultDel === true) {
                        var url = '{{route("tw.payorder.store")}}';
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: {
                                type: 1,
                                _token: "{{csrf_token()}}",
                                pay_type: 1,
                                activity_id: id
                            },
                            dataType: "json",
                            error: function (data) {
                                $.amaran({'message': "服务器繁忙, 请联系管理员！"});
                                return;
                            },
                            success: function (result) {
                                if (result.status == 1) {
                                    $.pjax({url: result.url, container: '#pjax-container', fragment: '#pjax-container'})
                                } else {
                                    $.amaran({'message': result.info});
                                }
                            },
                        })
                    }
                });
            })


            $(".adddays").on('click', function () {
                var message = '<input class="form-control"  id="add_days" />'
                var id = $(this).attr('data-id');
                dialog("续费天数", message, function (resultDel) {
                    if (resultDel === true) {
                        var add_days = $("#add_days").val().trim();
                        var url = '{{route("tw.payorder.store")}}';
                        if (/^\d+$/.test(add_days) == false) {
                            $.amaran({'message': "请输入有效天数"});
                            return;
                        }
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: {
                                type: 2,
                                _token: "{{csrf_token()}}",
                                pay_type: 1,
                                activity_id: id,
                                days: add_days
                            },
                            dataType: "json",
                            error: function (data) {
                                $.amaran({'message': "服务器繁忙, 请联系管理员！"});
                                return;
                            },
                            success: function (result) {
                                if (result.status == 1) {
                                    $.pjax({url: result.url, container: '#pjax-container', fragment: '#pjax-container'})
                                } else {
                                    $.amaran({'message': result.info});
                                }
                            },
                        })
                    }

                })
            })
            //发布活动
            $('.release').on('click', function () {
                var url = $(this).attr('data-url');
                var state = $(this).attr('data-state');
                if (state == 1) {
                    $.amaran({'message': "活动已发布，请勿重复发布！"});
                } else {
                    $.ajax({
                        url: url,
                        type: 'get',
                        dataType: "json",
                        error: function (data) {
                            $.amaran({'message': "服务器繁忙, 请联系管理员！"});
                            return;
                        },
                        success: function (result) {
                            if (result.status == 1) {
                                $.amaran({'message': result.info});
                                $.pjax({url: result.url, container: '#pjax-container', fragment: '#pjax-container'})
                            } else {
                                $.amaran({'message': result.info});
                            }
                        },
                    })
                }
            })
        })
    </script>
@endsection
