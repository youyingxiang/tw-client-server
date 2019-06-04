@extends('tw::layout.base',['header' => "活动",'pageTitle'=>'活动',"pageBtnName"=>'活动列表'])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                        <div class="pull-left">
                            {!! button(route('tw.activity.create'),'create') !!}
                            {!! button(route('tw.activity.destroy','all'),'delete_all') !!}
                        </div>
                        @include('tw::layout.search')
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover table-sort">
                            <tr>
                                <th width="35"><input type="checkbox" class="minimal checkbox-toggle"></th>
                                <th>ID</th>
                                <th>活动名称</th>
                                <th>logo</th>
                                <th>评分方式</th>
                                <th>背景图片</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            @foreach($aData as $vo)
                            <tr>
                                <td style="vertical-align:middle"><input type="checkbox" name="id[]" value="{{$vo['id']}}" class="minimal"></td>
                                <td style="vertical-align:middle">{{$vo['id']}}</td>
                                <td style="vertical-align:middle"><span class="editable" data-pk="{{$vo['id']}}}" data-name="title" data-url="" >{{$vo['title']}}</span></td>
                                <td style="vertical-align:middle"><img src="{{$vo['logo']}}" style="width:40px;border-radius:40%;" /></td>
                                <td style="vertical-align:middle">{{get_score_type($vo['score_type'])}}</td>
                                <td style="vertical-align:middle"><img src="{{$vo['banner']}}" style="width:40px;border-radius:40%;" /></td>
                                <td style="vertical-align:middle">{{$vo['created_at']}}</td>
                                <td style="vertical-align:middle">
                                    {!! button(route('tw.activity.edit',$vo['id']),'edit') !!}
                                    {!! button(route('tw.activity.destroy',$vo['id']),'delete',$vo['id']) !!}
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
