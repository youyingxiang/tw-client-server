<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{Tw::admin()['img'] ?: tw_asset("//vendor/tw/global/face/default.png")}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{Tw::admin()['name']}}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> 在线</a>
            </div>
        </div>
        <!--搜索-->
        <div class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" id="search-btn-value" class="form-control" placeholder="搜索">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="search-btn-menu btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(function () {
                    $(".search-btn-menu ").on('click', function () {
                        var data_value = $("#search-btn-value").val().trim()
                        $('.menu_check').each(function () {
                            var obj = $(this)
                            var search_content = obj.html()
                            if (search_content.indexOf(data_value) >= 0) {
                                if (obj.parent().parent().hasClass('treeview-menu')) {
                                    if (obj.parent().parent().parent().hasClass('active') == false) {
                                        obj.parent().parent().prev().trigger('click')
                                        obj.trigger('click')
                                    } else {
                                        obj.trigger('click')
                                    }
                                } else {
                                    obj.trigger('click');
                                }
                                return false
                            }
                        })
                    })
                })
            })
        </script>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            @include('tw::layout.menu',['items'=>Tw::getMenu()])
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>