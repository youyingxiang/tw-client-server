@extends('tw::layout.base',['header' => "活动",'pageTitle'=>'活动',"pageBtnName"=>'活动列表'])
@section('content')
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/css/my_style.css") }}">
    {{--样式--}}


    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    {{--切换--}}
                    <div class="tab_act">
                        <ul>
                            <li onclick="act_pt()" id="act_pt" style="background: #3c8dbc;color: #fff">普通活动</li>
                            <li onclick="act_gj()" id="act_gj">高级活动</li>
                            <a href="{!!route('tw.activity.create')!!}">创建新活动</a>
                        </ul>
                    </div>
                    {{--普通--}}
                    <div class="actvity_pt" id="putong">
                        <ul>
                            @foreach($aData as $vo)
                                <li>
                                    <div class="pt_left">
                                        <h4>{{$vo['title']}}</h4>
                                        <p style="color: #fff">期限:{!! $vo['term'] !!}</p>
                                    </div>
                                    <div class="pt_right">
                                        <div class="pt_ico"><a href="{!!route('tw.activity.edit',$vo['id'])!!}"><i
                                                        class="img_1"></i>
                                                <p>活动设置</p></a></div>
                                        <div class="pt_ico"><a target="_blank"
                                                               href="{!!route('tw.home',$vo['id'])!!}"><i
                                                        class="img_2"></i>
                                                <p>大屏幕</p></a></div>
                                        <div class="pt_ico"><a
                                                    href="{!!route('tw.judges.index')!!}?activity_id={{$vo['id']}}"><i
                                                        class="img_3"></i>
                                                <p>评委设置</p></a></div>
                                        <div class="pt_ico"><a
                                                    href="{!!route('tw.player.index')!!}?activity_id={{$vo['id']}}"><i
                                                        class="img_4"></i>
                                                <p>选手设置</p></a></div>
                                        <div class="pt_ico"><i class="img_5"></i>
                                            <p>屏幕控制</p></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{--高级--}}
                    <div class="actvity_pt" id="gaoji" style="display: none">
                        <ul>
                            @foreach($aData as $vo)
                                <li>
                                    <div class="pt_left">
                                        <h4>{{$vo['title']}}</h4>
                                        <p style="color: #fff">期限:{!! $vo['term'] !!}</p>
                                    </div>
                                    <div class="pt_right">
                                        <div class="pt_ico"><a href="{!!route('tw.activity.edit',$vo['id'])!!}"><i
                                                        class="img_1"></i>
                                                <p>活动设置</p></a></div>
                                        <div class="pt_ico"><a target="_blank"
                                                               href="{!!route('tw.home',$vo['id'])!!}"><i
                                                        class="img_2"></i>
                                                <p>大屏幕</p></a></div>
                                        <div class="pt_ico"><a
                                                    href="{!!route('tw.judges.index')!!}?activity_id={{$vo['id']}}"><i
                                                        class="img_3"></i>
                                                <p>评委设置</p></a></div>
                                        <div class="pt_ico"><a
                                                    href="{!!route('tw.player.index')!!}?activity_id={{$vo['id']}}"><i
                                                        class="img_4"></i>
                                                <p>选手设置</p></a></div>
                                        <div class="pt_ico"><i class="img_5"></i>
                                            <p>屏幕控制</p></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="box-footer clearfix">
                        {{ $aData->links() }}
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
        }

        function act_gj() {
            document.getElementById("act_gj").style.cssText = 'background:#3c8dbc;color:#fff;';
            document.getElementById("act_pt").style.cssText = 'background:none;color:#000;';
            document.getElementById("gaoji").style.cssText = 'display:block';
            document.getElementById("putong").style.cssText = 'display:none';
        }
    </script>
@endsection
