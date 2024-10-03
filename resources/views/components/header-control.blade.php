<section id="header_control">
    <div class="_wrapper">
        <a href="" class="catalog_btn controll open_cat_menu"><span>Каталог</span></a>

        <form class="header_search_form serch_form" action="{{route('show_search_page')}}">
            <button class="header_search_button pi florida_lins" type="submit"></button>
            <input class="header_search_input search__input" placeholder="Поиск" type="text" name="s" value="" id="">
            <div class="sub-load"></div>
            <div class="sub-sclose"></div>
            <div class="preSearchWrap"><div class="preSearchWrap_panel"></div></div>
        </form>

        <x-phone></x-phone>

        {{-- <x-messanger></x-messanger> --}}

        <a href="{{route('favorites')}}" class="favorites_head pi florida_like"><span>Избранное | <span class="favorites_counter">0</span></span></a>
        <bascet-and-counter></bascet-and-counter>
    </div>
</section>
