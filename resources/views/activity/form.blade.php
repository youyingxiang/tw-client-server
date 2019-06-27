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
                <form class="form-horizontal" method="POST" action="@if(empty($aData)){{route('tw.activity.store')}}@else{{tw_route('tw.activity.update',(int)$aData['id'])}}@endif" onsubmit="return false" >
                    {{ csrf_field() }}
                    @if(!empty($aData['id']))
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
                                    <label style="font-size: 12px;color: #999;text-align: left" class="col-sm-2 control-label">限制18字以内</label>
                                </div>

                                @if(empty($aData))
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">活动等级</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" name="level" style="width:100%;">
                                            @foreach(get_activity_level() as $k => $v)
                                                <option @if(!empty($aData['level']) && $k == $aData['level'])selected="selected"@endif value="{{$k}}">{{$v}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

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
                                            <input class="form-control" name="banner" value="{{$aData['banner']??asset("/vendor/tw/home/img/screen_bg.jpg")}}"  placeholder="活动背景" >
                                            <span class="input-group-btn">
                                        <a href="{{tw_asset($aData['banner'] ?? asset("/vendor/tw/home/img/screen_bg.jpg") )}}" target="_blank" >
                                            <img src="{{tw_asset($aData['banner'] ?? asset("/vendor/tw/home/img/screen_bg.jpg"))}}" style="height:34px; width:68px;" />
                                        </a>
                                        <button class="btn btn-success btn-flat up_img" type="button">
                                            <i class="fa fa-cloud-upload"> 上传</i>
                                        </button></span>

                                        </div>
                                    </div>
                                    <label style="font-size: 12px;color: #999;text-align: left" class="col-sm-2 control-label">建议尺寸：1920*1068</label>
                                </div>
                                @if(!empty($aData))
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">活动续费</label>

                                        <div class="col-sm-7">
                                            @if($aData['level'] == 1)
                                            <a class="btn btn-default" id="level2"> 升级为高级活动</a>
                                            @endif
                                            <a @if($aData['level'] == 1) style="margin-left:20px" @endif class="btn btn-default" id="adddays"> 增加活动天数</a>
                                        </div>

                                </div>
                                @endif

                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-7" style="padding-left: 3px">
                                <div class="btn-group pull-right">
                                    <button style="width: 120px;" type="submit" class="btn btn-info pull-right submits" data-loading-text="&lt;i class='fa fa-spinner fa-spin '&gt;&lt;/i&gt; 提交">提交</button>
                                </div>
                                <div class="btn-group pull-left">
                                    <button style="width: 120px;" type="reset" class="btn btn-warning">重置</button>
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
                dialog("活动升级","确认升级高级活动？",function (resultDel) {
                    if(resultDel === true) {
                        var url = '{{route("tw.payorder.store")}}';
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: {
                                type: 1,
                                _token: "{{csrf_token()}}",
                                pay_type: 1,
                                activity_id: "{{$aData['id']}}"
                            },
                            dataType: "json",
                            error: function (data) {
                                $.amaran({
                                    content:{
                                        title:'通知',
                                        message: "服务器繁忙",
                                        info:'',
                                        icon:'fa fa-warning'
                                    },
                                    theme:'awesome error',
                                    position  :'top right'
                                });
                                return;
                            },
                            success: function (result) {
                                if (result.status == 1) {
                                    $.pjax({url: result.url, container: '#pjax-container', fragment: '#pjax-container'})
                                } else {
                                    $.amaran({
                                        content:{
                                            title:'通知',
                                            message: result.info,
                                            info:'',
                                            icon:'fa fa-warning'
                                        },
                                        theme:'awesome error',
                                        position  :'top right'
                                    });
                                }
                            },
                        })
                    }
                });
            })
            
            
            

            $("#adddays").on('click',function(){
                var message = '<input class="form-control"  id="add_days" />'
                dialog("续费天数",message,function (resultDel) {
                    if(resultDel === true) {
                        var add_days = $("#add_days").val().trim();
                        var url = '{{route("tw.payorder.store")}}';
                        if (/^\d+$/.test(add_days) == false) {
                            $.amaran({
                                content:{
                                    title:'通知',
                                    message: "请输入有效天数！",
                                    info:'',
                                    icon:'fa fa-warning'
                                },
                                theme:'awesome error',
                                position  :'top right'
                            });
                            return;
                        }
                        $.ajax({
                            url: url,
                            type: 'post',
                            data: {
                                type: 2,
                                _token: "{{csrf_token()}}",
                                pay_type: 1,
                                activity_id: "{{$aData['id']}}",
                                days: add_days
                            },
                            dataType: "json",
                            error: function (data) {
                                $.amaran({
                                    content:{
                                        title:'通知',
                                        message: "服务器繁忙",
                                        info:'',
                                        icon:'fa fa-warning'
                                    },
                                    theme:'awesome error',
                                    position  :'top right'
                                });
                                return;
                            },
                            success: function (result) {
                                if (result.status == 1) {
                                    $.pjax({url: result.url, container: '#pjax-container', fragment: '#pjax-container'})
                                } else {
                                    $.amaran({
                                        content:{
                                            title:'通知',
                                            //message:'申请成功',
                                            message: result.info,
                                            info:'',
                                            icon:'fa fa-warning'
                                        },
                                        theme:'awesome error',
                                        position  :'top right'
                                    });
                                }
                            },
                        })
                    }

                })
            })
            @endif

        })
    </script>
@endsection
