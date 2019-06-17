@extends('tw::layout.base',['header' => "活动管理",'pageTitle'=>'评委',"pageBtnName"=>'活动列表'])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border screen-box">
                        <h3 class="box-title"></h3>
                        <div class="pull-left">
                            {!! button(route('tw.judges.create'),'create') !!}
                            {!! button(route('tw.judges.destroy','all'),'delete_all') !!}
                        </div>
                        @include('tw::layout.search')
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover table-sort">
                            <tr>
                                <th width="35"><input type="checkbox" class="minimal checkbox-toggle"></th>
                                <th>ID{!! table_sort('id') !!}</th>
                                <th>评委名称</th>
                                <th>图像</th>
                                <th>所属活动</th>
                                <th>评委二维码</th>
                                <th>添加日期</th>
                                <th>操作</th>
                            </tr>
                            @foreach($aData as $vo)
                                <tr>
                                    <td style="vertical-align:middle"><input type="checkbox" name="id[]" value="{{$vo['id']}}" class="minimal"></td>
                                    <td style="vertical-align:middle">{{$vo['id']}}</td>
                                    <td style="vertical-align:middle"><span class="editable" data-pk="{{$vo['id']}}" data-name="name" data-url="{{route('tw.judges.update',$vo['id'])}}" >{{$vo['name']}}</span></td>
                                    <td style="vertical-align:middle"><img src="{{$vo['img']}}" style="width:40px;border-radius:40%;" /></td>
                                    <td style="vertical-align:middle">{{$vo->activity->title}}</td>
                                    <td style="vertical-align:middle">{!! $vo['qr_code'] !!}</td>
                                    <td style="vertical-align:middle">{{$vo['created_at']}}</td>
                                    <td style="vertical-align:middle">
                                        {!! button(route('tw.judges.edit',$vo['id']),'edit') !!}
                                        {!! button(route('tw.judges.destroy',$vo['id']),'delete',$vo['id']) !!}
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
