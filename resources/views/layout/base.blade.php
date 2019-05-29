<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Tw::getTitle() }} </title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="renderer" content="webkit">
    {!! Tw::css() !!}
    <script src="{{ Tw::jQuery() }}"></script>
    {!! Tw::headerJs() !!}
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
    @yield('content')
</body>
    {!! Tw::js() !!}
</html>