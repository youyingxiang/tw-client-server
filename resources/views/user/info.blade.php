@extends('tw::layout.base',['header' => "个人中心",'pageTitle'=>'个人中心',"pageBtnName"=>'用户信息'])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="POST" action="" onsubmit="return false" >
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$user['id']}}" />
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">基本参数</a></li>
                            <li class="pull-right"><a href="javascript:history.back(-1)" class="btn btn-sm" style="padding:10px 2px;"><i class="fa fa-list"></i> 返回</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">昵称</label>
                                    <div class="col-sm-7"><input class="form-control" name="name" value="{{$user['name']}}"  placeholder="姓名"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">手机号</label>
                                    <div class="col-sm-7"><input class="form-control" name="phone" value="{{$user['phone']}}" placeholder="手机号"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">邮箱</label>
                                    <div class="col-sm-7"><input class="form-control" name="email" value="{{$user['email']}}" placeholder="邮箱"></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">微信</label>
                                    <div class="col-sm-7"><input class="form-control" name="wechat" value="{{$user['wechat']}}" placeholder="微信"></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">QQ</label>
                                    <div class="col-sm-7"><input class="form-control" name="qq" value="{{$user['qq']}}" placeholder="QQ"></div>
                                </div>

                                <input type="hidden" name="img" value="{{asset("/vendor/tw/global/face/default.png")}}"  placeholder="图像" >


                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-7">
                                <div class="btn-group pull-right">
                                    <button type="submit" class="btn btn-info pull-right submits" data-loading-text="&lt;i class='fa fa-spinner fa-spin '&gt;&lt;/i&gt; 提交">提交</button>
                                </div>
                                <div class="btn-group pull-left">
                                    <button type="reset" class="btn btn-warning">重置</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
