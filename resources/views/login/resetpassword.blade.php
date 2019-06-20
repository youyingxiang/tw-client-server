<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{Tw::getTitle()}} | 找回密码</title>
    <!-- Tell the browser to be responsive to screen width -->

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/bootstrap/css/font-awesome.min.css") }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/system/dist/css/AdminLTE.min.css") }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/iCheck/square/blue.css") }}">
    {{--MY STYLE--}}
    <link rel="stylesheet" href="{{ tw_asset("/vendor/tw/global/css/my_style.css") }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body id="login_bg" class="hold-transition login-page" @if(config('tw.login_background_image'))style="background: url({{config('tw.login_background_image')}}) no-repeat;background-size: cover;"@endif>
<div class="login-box">
{{--<div class="login-logo">--}}
{{--<a href="{{ tw_base_path('tw-server') }}"><b>{{config('tw.name',"后台管理系统")}}</b></a>--}}
{{--</div>--}}
<!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><b>天维</b>评分系统</p>

        <form action="{{ route("tw.resetpassword")}}" method="post" id="login_input" >
            {{--用户名--}}

            {{--END--}}
            <div class="form-group has-feedback {!! !$errors->has('phone') ?: 'has-error' !!}">

                @if($errors->has('phone'))
                    @foreach($errors->get('phone') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif

                <input type="text" class="form-control" placeholder="请输入手机号" name="phone" value="{{ old('phone') }}">
                <span id="login-ico-input" class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">

                @if($errors->has('password'))
                    @foreach($errors->get('password') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif

                <input type="password" class="form-control"  placeholder="请输入新的密码" name="password">
                <span id="login-ico-input" class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback {!! !$errors->has('password_confirmation') ?: 'has-error' !!}">

                @if($errors->has('password_confirmation'))
                    @foreach($errors->get('password_confirmation') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif

                <input type="password" class="form-control"  placeholder="请确认新的密码" name="password_confirmation">
                <span id="login-ico-input" class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            {{--验证码--}}
            <div style="overflow: hidden;" class="form-group has-feedback {!! !$errors->has('code') ?: 'has-error' !!}">

                @if($errors->has('code'))
                    @foreach($errors->get('code') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif

                <input type="text" class="form-control" placeholder="请输入验证码" name="code" style="width: 60%;float: left" >
                <a style="background: #FF9800;border: none; margin-top:5pt;width: 30%;float: right" type="submit" class="btn code btn-primary btn-block btn-flat">获取验证码</a>
                <span id="login-ico-input" class="glyphicon glyphicon-comment form-control-feedback"></span>
            </div>
            {{--END--}}
            <div class="row">
                <div class="col-xs-4" style="width: 100%">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button style="width: 100%" type="submit" class="btn btn-primary btn-block btn-flat">确认找回密码</button>
                </div>
                <!-- /.col -->
            </div>
            <div class="col-xs-zc">
                <p><a href="{{route("tw.login")}}">返回登陆</a></p>
            </div>
        </form>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ tw_asset("/vendor/tw/global/jQuery/jquery-2.2.3.min.js")}} "></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ tw_asset("/vendor/tw/global/bootstrap/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ tw_asset("/vendor/tw/global/iCheck/icheck.min.js")}}"></script>
<script src="{{ tw_asset("/vendor/tw/home/js/public.js")}}"></script>
<script>
    $(function () {

        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
        /**
         * @see 点击发送短信
         */
        $(".code").on('click',function(){
            var phone = $('input[name="phone"]').val().trim();
            if (phone) {
                if (isPhoneNo(phone)) {
                    ajaxGetCode(phone);
                } else {
                    alert("手机号码格式不正确！");
                }
            } else {
                alert("手机号码不能为空");
            }
        })

        /**
         * @see ajax 获取短信
         * @param $phone
         */
        function ajaxGetCode(phone) {
            $.ajax({
                url: "{{route('tw.resetSendMsg')}}",
                type:'post',
                dataType: "json",
                data:{ id:1,resetphone:phone,_method:'post' ,_token:"{{csrf_token()}}"},
                error:function(data){
                    alert("服务器繁忙, 请联系管理员！");
                    return;
                },
                success:function(result){
                    if(result.status == 1){
                        settime($(".code"));
                    } else {
                        alert(result.info)
                    }
                },
            })
        }
        /**
         * [countdown 60S验证码]
         */
        var countdown = 60;
        function settime(obj) {
            if (countdown == 0) {
                obj.html("获取验证码");
                obj.css('background','#FF9800');
                obj.attr('disabled',"true");
                countdown = 60;
                return;
            } else {
                obj.css('background','#666');
                obj.html("重新发送(" + countdown + ")");
                obj.removeAttr("disabled");
                countdown--;
            }
            setTimeout(function(){settime(obj)},1000)
        }

    });
</script>
</body>
</html>
