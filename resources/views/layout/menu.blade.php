<li class="header">菜单</li>
@foreach($items as $item)
@if($item['level'] == 0)
<li class="treeview">
    <a class="menu_check" href="{{tw_base_path($item['url'])}}">
        <i class="fa {{$item['icon']}}"></i><span>{{$item['title']}}</span>
    </a>
</li>
@endif
@endforeach