@extends('tw::layout.base',['header' => "控制台",'pageTitle'=>'控制台',"pageBtnName"=>'控制台'])
@section('content')
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/css/my_style.css") }}">
    {{--样式--}}


    <section class="content">
        <div class="control_box">
            <ul>
                <a href="">
                    <li>跳转大屏幕</li>
                </a>
                <a href="">
                    <li>跳转排名页</li>
                </a>
                <a href="">
                    <li>推送下一个选手</li>
                </a>
                <a href="">
                    <li>选手列表</li>
                </a>
            </ul>
        </div>
    </section>

@endsection
