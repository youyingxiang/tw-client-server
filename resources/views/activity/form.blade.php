@if(!empty($aData['id']))
    {{$btname = '修改活动'}}
@else
    {{$btname = '增加活动'}}
@endif
@extends('tw::layout.base',['header' => "活动 ".$btname,'pageTitle'=>'活动',"pageBtnName"=>$btname])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="POST" action="@if(empty($aData)){{route('tw.activity.store')}}@else{{tw_route('tw.activity.update',$aData['id'])}}@endif" onsubmit="return false" >
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
                                        <a href="{{tw_asset($aData['logo'] ?? asset("/vendor/tw/global/face/no-image.png") )}}" target="_blank" >
                                            <img src="{{tw_asset($aData['logo'] ?? asset("/vendor/tw/global/face/no-image.png"))}}" style="height:34px; width:68px;" />
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
                                        <a href="{{tw_asset($aData['banner'] ?? asset("/vendor/tw/global/face/no-image.png") )}}" target="_blank" >
                                            <img src="{{tw_asset($aData['banner'] ?? asset("/vendor/tw/global/face/no-image.png"))}}" style="height:34px; width:68px;" />
                                        </a>
                                        <button class="btn btn-success btn-flat up_img" type="button">
                                            <i class="fa fa-cloud-upload"> 上传</i>
                                        </button></span>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($aData))
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">活动续费</label>
                                    <div class="col-sm-7"><a class="btn btn-default" id="level2"> 升级为高级活动</a><a style="margin-left:20px" class="btn btn-default" id="adddays"> 增加活动天数</a></div>
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
    <script type="text/javascript">
        $(function() {
            function dialog(title,message,func) {
                BootstrapDialog.confirm({
                    onshow: function (obj) {
                        var cssConf = {};
                        cssConf['width'] = 300;
                        if (cssConf) {
                            obj.getModal().find('div.modal-dialog').css(cssConf);
                        }
                    },
                    title: title,
                    message: message,
                    btnCancelLabel: '取消',
                    btnOKLabel: '确定',
                    callback: func
                });
            }
            @if (!empty($aData['id']))
            $("#level2").on('click',function(){
                dialog("活动升级","确认升级高级活动？",function () {
                    var url = '{{route("tw.payorder.store")}}';
                    $.ajax({
                        url: url,
                        type:'post',
                        data:{
                            type:1,
                            _token:"{{csrf_token()}}",
                            pay_type:1,
                            activity_id:"{{$aData['id']}}"
                        },
                        dataType: "json",
                        error:function(data){
                            $.amaran({'message':"服务器繁忙, 请联系管理员！"});
                            return;
                        },
                        success:function(result){
                            if(result.status == 1){
                                $.pjax({url: result.url, container: '#pjax-container', fragment:'#pjax-container'})
                            } else {
                                $.amaran({'message':result.info});
                            }
                        },
                    })
                });
            })
            
            
            

            $("#adddays").on('click',function(){
                var message = '<input class="form-control"  id="add_days" />'
                dialog("续费天数",message,function () {
                    var add_days = $("#add_days").val().trim();
                    var url = '{{route("tw.payorder.store")}}';
                    if (/^\d+$/.test(add_days) == false) {
                        $.amaran({'message':"请输入有效天数"});
                        return;
                    }
                    $.ajax({
                        url: url,
                        type:'post',
                        data:{
                            type:2,
                            _token:"{{csrf_token()}}",
                            pay_type:1,
                            activity_id:"{{$aData['id']}}",
                            days:add_days
                        },
                        dataType: "json",
                        error:function(data){
                            $.amaran({'message':"服务器繁忙, 请联系管理员！"});
                            return;
                        },
                        success:function(result){
                            if(result.status == 1){
                                $.pjax({url: result.url, container: '#pjax-container', fragment:'#pjax-container'})
                            } else {
                                $.amaran({'message':result.info});
                            }
                        },
                    })

                })
            })
            @endif

        })
    </script>
@endsection
