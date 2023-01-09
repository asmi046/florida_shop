<div class="mobile_bottom_menu">

    <a href="{{route('home')}}" class="bm_btn">
        <div class="icon bottom_menu_icon_home"></div>
        <p>Главная</p>
    </a>

    <a href="{{route('favorites')}}" class="bm_btn">
        <div class="icon bottom_menu_icon_favorites"></div>
        <p>Избранное</p>
    </a>

    <a href="{{route('bascet')}}" class="bm_btn">
        <div class="icon bascet_blk bottom_menu_icon_bascet">
            <span class="bascet_counter">0</span>
        </div>
        <p>Корзина</p>
    </a>

    <a href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="bm_btn open_cat_menu ">
        <div class="icon bottom_menu_icon_phone"></div>
        <p>Позвонить</p>
    </a>

    <a href="#" class="bm_btn open_cat_menu ">
        <div class="icon bottom_menu_icon_catalog"></div>
        <p>Каталог</p>
    </a>
</div>
