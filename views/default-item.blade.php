<li>
    <a href="{{ $item->link }}" title="{{ $item->title }}">
        <i class="{{ $item->icon }}"></i>
        <span>{{ $item->title }}</span>
    </a>

    @if($item->children->isNotEmpty())
        <ul class="treeview-menu">
            @each('menu::default-item', $item->children, 'item')
        </ul>
    @endif
</li>
