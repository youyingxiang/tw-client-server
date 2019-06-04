@extends('tw::layout.base',['header' => "活动",'pageTitle'=>'个人中心',"pageBtnName"=>'用户信息'])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="POST" action="@if(empty($aData)){{route('tw.activity.store')}}@else{{route('tw.activity.update',$aData['id'])}}@endif" onsubmit="return false" >
                    {{ csrf_field() }}
                    @if(!empty($aData['id']))
                    <input type="hidden" name="id" value="{{$aData['id']}}" />
                        {{ method_field('PUT') }}
                    @endif
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">基本参数</a></li>
                            <li class="pull-right"><a href="javascript:history.back(-1)" class="btn btn-sm" style="padding:10px 2px;"><i class="fa fa-list"></i> 返回</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">活动名称</label>
                                    <div class="col-sm-7"><input class="form-control" name="title" value="{{$aData['title']??''}}"  placeholder="活动名称"></div>
                                </div>



                                <div class="form-group">
                                    <label class="col-sm-2 control-label">活动LOGO</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input class="form-control" name="logo" value="{{$aData['logo']??''}}"  placeholder="活动LOGO" >
                                            <span class="input-group-btn">
                                        <a href="{{tw_asset($aData['logo'] ?? asset("vendor/tw/global/face/no-image.png") )}}" target="_blank" >
                                            <img src="{{tw_asset($aData['logo'] ?? asset("vendor/tw/global/face/no-image.png"))}}" style="height:34px; width:68px;" />
                                        </a>
                                        <button class="btn btn-success btn-flat up_img" type="button">
                                            <i class="fa fa-cloud-upload"> 上传</i>
                                        </button></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">评分方式</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" name="score_type" style="width:100%;">
                                            @foreach(get_score_type() as $k => $v)
                                                <option @if(!empty($aData['score_type']) && $k == $aData['score_type'])selected="selected"@endif value="{{$k}}">{{$v}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label">活动背景</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input class="form-control" name="banner" value="{{$aData['banner']??''}}"  placeholder="活动背景" >
                                            <span class="input-group-btn">
                                        <a href="{{tw_asset($aData['banner'] ?? asset("vendor/tw/global/face/no-image.png") )}}" target="_blank" >
                                            <img src="{{tw_asset($aData['banner'] ?? asset("vendor/tw/global/face/no-image.png"))}}" style="height:34px; width:68px;" />
                                        </a>
                                        <button class="btn btn-success btn-flat up_img" type="button">
                                            <i class="fa fa-cloud-upload"> 上传</i>
                                        </button></span>
                                        </div>
                                    </div>
                                </div>

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
