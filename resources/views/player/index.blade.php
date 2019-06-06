@extends('tw::layout.base',['header' => "活动管理",'pageTitle'=>'选手',"pageBtnName"=>'活动列表'])
@section('content')
    <style>
        .screen-box .box-tools .input-group .input-group-btn .btn{height:34px;}
        @media screen and (max-width:769px){
            .screen-box > .box-tools{position:static;}
            .screen-box > .pull-left{margin-bottom:10px;}
            .screen-box .input-group{width:100% !important;}
            .screen-box .select2-container{}
            .screen-box .box-tools .input-group .pull-right{margin-bottom:10px;}
            .screen-box .box-tools .input-group .pull-right,.screen-box .box-tools .input-group .pull-left,.screen-box .box-tools .input-group .pull-left .select2-container{width:100% !important;}
            .screen-box .box-tools .input-group .input-group-btn{vertical-align:bottom;}
        }
    </style>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border screen-box">
                        <h3 class="box-title"></h3>
                        <div class="pull-left">
                            {!! button(route('tw.player.create'),'create') !!}
                            {!! button(route('tw.player.destroy','all'),'delete_all') !!}
                        </div>
                        <div class="box-tools" style="top:10px;">
                            <form action="{{search_url('search')}}" method="GET" pjax-search="">
                                <div class="input-group input-group-sm" style="width:600px">
                                    <div class='pull-right' style="width:40%">
                                        <input type="text" name="search" class="form-control" value="{{request()->get('search')}}" placeholder="搜索" />
                                    </div>
                                    <div class='pull-left' style="width:60%">
                                        <select name="activity_id" class="form-control select2" placeholder="搜索">
                                            <option value="">不限</option>
                                            @foreach(Tw::admin()->activitys as $vo)
                                            <option @if(request()->get('activity_id') == $vo['id'])selected='selected' @endif value="{{$vo['id']}}">
                                            {{$vo['title']}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default sreachs"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover table-sort">
                            <tr>
                                <th width="35"><input type="checkbox" class="minimal checkbox-toggle"></th>
                                <th>ID</th>
                                <th>选手名称</th>
                                <th>图像</th>
                                <th>所属活动</th>
                                <th>最终得分</th>
                                <th>推送状态</th>
                                <th>修改日期</th>
                                <th>操作</th>
                            </tr>
                            @foreach($aData as $vo)
                                <tr>
                                    <td style="vertical-align:middle"><input type="checkbox" name="id[]" value="{{$vo['id']}}" class="minimal"></td>
                                    <td style="vertical-align:middle">{{$vo['id']}}</td>
                                    <td style="vertical-align:middle"><span class="editable" data-pk="{{$vo['id']}}" data-name="name" data-url="{{route('tw.player.update',$vo['id'])}}" >{{$vo['name']}}</span></td>
                                    <td style="vertical-align:middle"><img src="{{$vo['img']}}" style="width:40px;border-radius:40%;" /></td>
                                    <td style="vertical-align:middle">{{$vo->activity->title}}</td>
                                    <td style="vertical-align:middle"><span class="editable" data-pk="{{$vo['id']}}" data-name="score" data-url="{{route('tw.player.update',$vo['id'])}}" >{{$vo['score']}}</span></td>
                                    <td style="vertical-align:middle">
                                        <a href="javascript:void(0);" class='editimg fa @if($vo['push_state'] == 1)fa-check-circle text-green @else fa-times-circle text-red @endif'>
                                        </a>
                                    </td>
                                    <td style="vertical-align:middle">{{$vo['updated_at']}}</td>
                                    <td style="vertical-align:middle">
                                        {!! button(route('tw.player.push',$vo['id']),'push') !!}
                                        {!! button(route('tw.player.edit',$vo['id']),'edit') !!}
                                        {!! button(route('tw.player.destroy',$vo['id']),'delete',$vo['id']) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        {{ $aData->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
