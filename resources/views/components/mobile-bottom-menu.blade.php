<div class="mobile_bottom_menu">

    <a href="{{ route('home') }}" class="bm_btn">
        <svg class="sprite_icon">
            <use xlink:href="#rose"></use>
        </svg>
        <p>Главная</p>
    </a>

    <a href="{{ route('bascet') }}" class="bm_btn bm_btn_cart">

        <bascet-counter></bascet-counter>
        <svg class="sprite_icon">
            <use xlink:href="#cart"></use>
        </svg>
        <p>Корзина</p>
    </a>

    <a href="tel:{{ str_replace(['-', ' ', '(', ')'], '', $options['phone']) }}" class="bm_btn">
        <svg class="sprite_icon">
            <use xlink:href="#phone"></use>
        </svg>
        <p>Позвонить</p>
    </a>

    <a href="#" class="bm_btn open_cat_menu ">
        <svg id="menu_list_closet" class="sprite_icon">
            <use xlink:href="#menu_list"></use>
        </svg>
        <svg id="menu_list_open" class="sprite_icon">
            <use xlink:href="#menu_close"></use>
        </svg>
        <p>Каталог</p>
    </a>
</div>
