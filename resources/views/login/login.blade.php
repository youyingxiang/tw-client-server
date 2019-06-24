<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{Tw::getTitle()}} | 后台登陆</title>
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

        <form action="{{ tw_base_path('login') }}" method="post" id="login_input" >
            <div class="form-group has-feedback {!! !$errors->has('phone') ?: 'has-error' !!}">

                @if($errors->has('phone'))
                    @foreach($errors->get('phone') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif

                <input type="text" class="form-control" placeholder="请输入手机号" name="phone" value="{{ old('phone') }}">
                <span id="login-ico-input" class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}" style="margin-bottom: 0">

                @if($errors->has('password'))
                    @foreach($errors->get('password') as $message)
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
                    @endforeach
                @endif

                <input type="password" class="form-control" placeholder="请输入登陆密码" name="password">
                <span id="login-ico-input" class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <p style="line-height: 35px;margin: 0;">忘记密码？<a href="{{route('tw.resetpassword')}}">找回密码</a></p>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    @if(config('tw.auth.remember'))
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember" value="1" {{ (!old('phone') || old('remember')) ? 'checked' : '' }}>
                                记住我
                            </label>
                        </div>
                    @endif
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-primary btn-block btn-flat" style="margin-top: 3px">登陆</button>
                </div>
                <!-- /.col -->
            </div>
            <div class="col-xs-zc">
                <p>还没有账号？<a href="{{route('tw.register')}}">立即注册</a></p>
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
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
