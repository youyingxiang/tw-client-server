@extends('tw::layout.base',['header' => "消费",'pageTitle'=>'后台首页',"pageBtnName"=>'按钮'])
@section('content')
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/css/my_style.css") }}">
    <style>
        .content-header {
            display: none !important;
        }
    </style>
    {{--中心页面--}}
    <div class="sever_center">
        {{--个人信息--}}
        <div class="sever_user">
            <p>个人信息</p>
            <div class="sever_user_text">
                <img src="{{$user['img'] ?:tw_asset("/vendor/tw/global/face/default.png")}}" alt="">
                <ul>
                    <li><b>{{$user['name']}}</b></li>
                    <li class="sever_a1">手机号：{{$user['phone']}} </li>
                </ul>
            </div>
        </div>
        {{--订单信息--}}
        <div class="sever_order">
            <p class="sever_order_title"><a>消费记录</a></p>
            <div class="sever_order_list">
                <table id="example2" class="table table-bordered table-hover">
                    <tr>
                        <th>订单编号</th>
                        <th>订单信息</th>
                        <th>支付方式</th>
                        <th>状态</th>
                        <th>活动名称</th>
                        <th>金额</th>
                        <th>支付时间</th>
                        <th>操作</th>
                    </tr>
                    @foreach($oOrder as $v)
                    <tr>
                        <td>{{$v['order_no']}}</td>
                        <td>{{$v['order_info']}}</td>
                        <td>{{$v['s_pay_type']}}</td>
                        <td>{{$v['s_pay_state']}}</td>
                        <td>{{$v['activity_title']}}</td>
                        <td>{{$v['pay_amount']}}</td>
                        <td>{{$v['updated_at']}}</td>
                        <td>{!! $v['go_pay'] !!}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <div class="box-footer clearfix">
                {{ $oOrder->links() }}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $(".gopay").on('click',function () {
                window.location.href = $(this).attr('data-url');
            })
        })
    </script>
@endsection
