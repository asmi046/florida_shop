<section id="header_control">
    <div class="_wrapper">
        <a href="" class="catalog_btn controll open_cat_menu"><span>Каталог</span></a>

        <form class="header_search_form serch_form" action="">
            <button class="header_search_button" type="submit"></button>
            <input class="header_search_input search__input" placeholder="Поиск" type="text" name="" value="" id="">
            <div class="sub-load"></div>
            <div class="sub-sclose"></div>
            <div class="preSearchWrap"><div class="preSearchWrap_panel"></div></div>
        </form>

        <x-phone></x-phone>

        <x-messanger></x-messanger>

        <a href="{{route('favorites')}}" class="favorites_head"><span>Избранное | <span class="favorites_counter">0</span></span></a>
        <a href="{{route('bascet')}}" class="bascet_head">Корзина | <span class="bascet_counter">0</span> </a>
    </div>
</section>
