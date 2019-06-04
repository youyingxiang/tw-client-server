@extends('tw::layout.base',['header' => "测试",'pageTitle'=>'后台首页',"pageBtnName"=>'按钮'])
@section('content')
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("vendor/tw/global/css/my_style.css") }}">
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
                <img src="{{Tw::authLogic()->guard()->user()['img']}}" alt="">
                <ul>
                    <li><b>{{Tw::authLogic()->guard()->user()['name']}}</b><a href="">编辑昵称</a></li>
                    <li class="sever_a1">手机号：{{Tw::authLogic()->guard()->user()['phone']}} <a href="">修改手机</a></li>
                    <li class="sever_a1">密码：****** <a href="">修改密码</a></li>
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
                        <th>金额</th>
                        <th>时间</th>
                        <th>操作</th>
                    </tr>
                    <tr>
                        <td>2019060317210011</td>
                        <td>开通高级活动</td>
                        <td>微信支付</td>
                        <td>已付款</td>
                        <td>300.00</td>
                        <td>2019-06-03 17:21:55</td>
                        <td><a href="">删除</a></td>
                    </tr>
                    <tr>
                        <td>2019060317210011</td>
                        <td>开通高级活动</td>
                        <td>微信支付</td>
                        <td>已付款</td>
                        <td>300.00</td>
                        <td>2019-06-03 17:21:55</td>
                        <td><a href="">删除</a></td>
                    </tr>
                    <tr>
                        <td>2019060317210011</td>
                        <td>开通高级活动</td>
                        <td>微信支付</td>
                        <td>已付款</td>
                        <td>300.00</td>
                        <td>2019-06-03 17:21:55</td>
                        <td><a href="">删除</a></td>
                    </tr>
                    <tr>
                        <td>2019060317210011</td>
                        <td>购买天数1天</td>
                        <td>微信支付</td>
                        <td>已付款</td>
                        <td>300.00</td>
                        <td>2019-06-03 17:21:55</td>
                        <td><a href="">删除</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
