<li class="header">菜单</li>
@foreach($items as $item)
@if($item['level'] == 0)
<li class="treeview">
    <a class="menu_check" href="javascript:void(0);">
        <i class="fa {{$item['icon']}}"></i><span>{{$item['title']}}</span>
        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
    </a>
    <ul class="treeview-menu">
        @foreach($items as $item2)
            @if($item2['parent_id'] == $item['id'])
            <li>
                @if(url()->isValidUrl($item2['url']))
                    <a class="menu_check" href="{{ $item2['url'] }}" target="_blank">
                @else
                    <a class="menu_check" href="{{ tw_base_path($item2['url']) }}">
                @endif
                    <i class="fa {{$item2['icon']}}"></i>{{$item2['title']}}
                </a>
            </li>
            @endif
        @endforeach
    </ul>
</li>
@endif
@endforeach