<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{Tw::admin()['img'] ?: tw_asset("/vendor/tw/global/face/default.png")}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{Tw::admin()['name']}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> 在线</a>
            </div>
        </div>

        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @include('tw::layout.menu',['items'=>Tw::getMenu()])
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>