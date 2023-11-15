<div class="mobile_bottom_menu">

    <a href="{{route('home')}}" class="bm_btn">
        <div class="icon bottom_menu_icon_home  pi florida_home"></div>
        <p>Главная</p>
    </a>

    <a href="{{route('favorites')}}" class="bm_btn">
        <div class="icon bottom_menu_icon_favorites pi florida_like"></div>
        <p>Избранное</p>
    </a>

    <a href="{{route('bascet')}}" class="bm_btn">
        <div class="icon bascet_blk bottom_menu_icon_bascet pi florida_cart">
            {{-- <span class="bascet_counter">0</span> --}}
            <bascet-counter></bascet-counter>
        </div>
        <p>Корзина</p>
    </a>

    <a href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="bm_btn open_cat_menu ">
        <div class="icon bottom_menu_icon_phone pi florida_phone"></div>
        <p>Позвонить</p>
    </a>

    <a href="#" class="bm_btn open_cat_menu ">
        <div class="icon bottom_menu_icon_catalog pi florida_menu_burger"></div>
        <p>Каталог</p>
    </a>
</div>
