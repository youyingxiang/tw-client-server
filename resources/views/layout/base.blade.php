<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Tw::getTitle() }} @if($header) | {{ $header }}@endif</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="renderer" content="webkit">
    {!! Tw::css() !!}
    <script src="{{ Tw::jQuery() }}"></script>
    {!! Tw::headerJs() !!}
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
<div class="wrapper">
    @include('tw::layout.header')
    @include('tw::layout.sidebar')
    <div class="content-wrapper" id="pjax-container">
        <section class="content-header">
            <h1>{{$pageTitle}}</h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i>{{$pageBtnName}}</a></li>
            </ol>
        </section>
        @yield('content')
        {!! Tw::script() !!}
    </div>
    {!! Tw::fileUpload() !!}
    @include('tw::layout.footer')
</div>
</body>
{!! Tw::js() !!}
</html>