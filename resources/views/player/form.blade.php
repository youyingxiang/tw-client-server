@if(!empty($aData['id']))
    {{$btname = '修改选手'}}
@else
    {{$btname = '增加选手'}}
@endif
@extends('tw::layout.base',['header' => "活动 ".$btname,'pageTitle'=>'选手列表',"pageBtnName"=>$btname])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if(empty($aData))
                    @php $action = route('tw.player.store')@endphp
                @else
                    @php $action = route('tw.player.update',$aData['id']) @endphp
                @endif
                <form class="form-horizontal" method="POST" action="{{$action}}" onsubmit="return false" >
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
                                    <label class="col-sm-2 control-label">选手名称</label>
                                    <div class="col-sm-7"><input class="form-control" name="name" value="{{$aData['name']??''}}"  placeholder="选手名称"></div>
                                </div>



                                <div class="form-group">
                                    <label class="col-sm-2 control-label">选手头像</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input class="form-control" name="img" value="{{$aData['img'] ?? asset("/vendor/tw/global/face/default.png") }}"  placeholder="选手头像" >
                                            <span class="input-group-btn">
                                        <a href="{{tw_asset($aData['img'] ?? asset("/vendor/tw/global/face/no-image.png") )}}" target="_blank" >
                                            <img src="{{tw_asset($aData['img'] ?? asset("/vendor/tw/global/face/no-image.png"))}}" style="height:34px; width:68px;" />
                                        </a>
                                        <button class="btn btn-success btn-flat up_img" type="button">
                                            <i class="fa fa-cloud-upload"> 上传</i>
                                        </button></span>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($aData['activity_id']))
                                    <input type="hidden" name="activity_id" value="{{$aData['activity_id']}}" />
                                @elseif(!empty(request()->get('activity_id')))
                                    <input type="hidden" name="activity_id" value="{{request()->get('activity_id')}}" />
                                @else
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">所属活动</label>
                                        <div class="col-sm-7">
                                            <select class="form-control select2" name="activity_id" style="width:100%;">
                                                @foreach($oActivitys as  $v)
                                                <option value="{{$v['id']}}">{{$v['title']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($aData['activity_id']))
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">最终得分</label>
                                    <div class="col-sm-7"><input class="form-control" name="score" value="{{$aData['score']??0}}"  placeholder="最终得分"></div>
                                </div>
                                @endif



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
