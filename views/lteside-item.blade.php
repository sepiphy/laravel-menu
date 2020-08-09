<li class="{{ $item->active ? ' active' : '' }}{{ $item->open ? ' treeview' : '' }}">
    <a href="{{ $item->link }}" title="{{ $item->title }}">
        <i class="{{ $item->icon }}"></i>
        <span>{{ $item->title }}</span>
        @if($item->children->isNotEmpty())
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        @endif
    </a>

    @if($item->children->isNotEmpty())
        <ul class="treeview-menu">
            @each('menu::lteside-item', $item->children, 'item')
        </ul>
    @endif
</li>
