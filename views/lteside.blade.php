<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <!-- <li class="header">
                <span class="text-uppercase">
                    {{-- $options[''] --}}
                </span>
            </li> -->

            @each('menu::lteside-item', $menu->items, 'item')
        </ul>
    </section>
</aside>
