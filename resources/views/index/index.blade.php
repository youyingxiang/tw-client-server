@extends('tw::layout.base',['header' => "测试",'pageTitle'=>'后台首页',"pageBtnName"=>'按钮'])
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"></h3>
                        <div class="pull-left">
                            <a href="/admin/Admin/add.html" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> 增加</a>
                            <a class="btn btn-sm btn-danger delete-all" href="javascript:void(0);" data-url="/admin/Admin/delete.html"><i class="fa fa-trash"></i> 删除选中</a>
                        </div>
                        <div class="box-tools" style="top:10px;">
                            <form action="/admin/Admin/lst.html?/admin/Admin/lst_html=" method="GET" pjax-search="">
                                <div class="input-group input-group-sm" style="width:200px;">
                                    <input type="text" name="search" class="form-control pull-right" value="" placeholder="搜索">
                                    <div class="input-group-btn"><button type="submit" class="btn btn-default sreachs"><i class="fa fa-search"></i></button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-hover table-sort">
                            <tbody><tr>
                                <th width="35"><div class="icheckbox_minimal-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" class="minimal checkbox-toggle" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div></th>
                                <th>ID<a class="fa fa-sort" href="/admin/Admin/lst.html?/admin/Admin/lst_html=&amp;_sort=id,asc"></a></th>
                                <th>姓名<a class="fa fa-sort" href="/admin/Admin/lst.html?/admin/Admin/lst_html=&amp;_sort=admin_name,asc"></a></th>
                                <th>图像</th>
                                <th>角色</th>
                                <th>账号<a class="fa fa-sort" href="/admin/Admin/lst.html?/admin/Admin/lst_html=&amp;_sort=account,asc"></a></th>
                                <th>性别</th>
                                <th>状态</th>
                                <th>上一次登陆时间<a class="fa fa-sort" href="/admin/Admin/lst.html?/admin/Admin/lst_html=&amp;_sort=last_time,asc"></a></th>
                                <th width="204">操作</th>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><div class="icheckbox_minimal-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" name="id[]" value="1" class="minimal" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div></td>
                                <td style="vertical-align:middle">1</td>
                                <td style="vertical-align:middle"><span class="editable editable-click" data-pk="1" data-name="admin_name" data-url="/admin/Admin/edit/id/1.html">游兴祥</span></td>
                                <td style="vertical-align:middle"><img src="/uploads/image/20180811/6c9d228310c44b5be070737af6263d25.jpg" style="width:40px;border-radius:40%;"></td>
                                <td style="vertical-align:middle">超级管理员</td>
                                <td style="vertical-align:middle"><span class="editable editable-click" data-pk="1" data-name="account" data-url="/admin/Admin/edit/id/1.html">1365831278@qq.com</span></td>
                                <td style="vertical-align:middle">
                                    <a href="javascript:void(0);" data-id="1" data-field="sex" data-value="1" data-url="/admin/Admin/edit/id/1.html" class="editimg sex fa fa-male text-green"></a>
                                </td>
                                <td style="vertical-align:middle">
                                    <a href="javascript:void(0);" data-id="1" data-field="state" data-value="1" data-url="/admin/Admin/edit/id/1.html" class="editimg fa fa-check-circle text-green"></a>
                                </td>
                                <td style="vertical-align:middle">2019-05-29 16:07:15</td>
                                <td style="vertical-align:middle">
                                    <a class="btn btn-primary btn-xs" href="/admin/Admin/edit/id/1.html"><i class="fa fa-edit"></i> 编辑</a>                            </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle"><div class="icheckbox_minimal-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" name="id[]" value="4" class="minimal" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div></td>
                                <td style="vertical-align:middle">4</td>
                                <td style="vertical-align:middle"><span class="editable editable-click" data-pk="4" data-name="admin_name" data-url="/admin/Admin/edit/id/4.html">测试</span></td>
                                <td style="vertical-align:middle"><img src="/uploads/image/20180811/c52fb7986c57e22c2f7b71790d38d1a2.jpg" style="width:40px;border-radius:40%;"></td>
                                <td style="vertical-align:middle">普通管理员</td>
                                <td style="vertical-align:middle"><span class="editable editable-click" data-pk="4" data-name="account" data-url="/admin/Admin/edit/id/4.html">YZ8w9nQTuS8KBCc1</span></td>
                                <td style="vertical-align:middle">
                                    <a href="javascript:void(0);" data-id="4" data-field="sex" data-value="1" data-url="/admin/Admin/edit/id/4.html" class="editimg sex fa fa-male text-green"></a>
                                </td>
                                <td style="vertical-align:middle">
                                    <a href="javascript:void(0);" data-id="4" data-field="state" data-value="1" data-url="/admin/Admin/edit/id/4.html" class="editimg fa fa-check-circle text-green"></a>
                                </td>
                                <td style="vertical-align:middle">2019-01-16 13:07:47</td>
                                <td style="vertical-align:middle">
                                    <a class="btn btn-primary btn-xs" href="/admin/Admin/edit/id/4.html"><i class="fa fa-edit"></i> 编辑</a>
                                    <a class="btn btn-danger btn-xs delete-one" href="javascript:void(0);" data-url="/admin/Admin/delete.html" data-id="4"><i class="fa fa-trash"></i> 删除</a>                            </td>
                            </tr>
                            </tbody></table>
                    </div>
                    <div class="box-footer clearfix">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
