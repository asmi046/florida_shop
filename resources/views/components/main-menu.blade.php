<ul class="main_menu">
    <li>
        <a class="catalog_menu_btn" href="{{ route('catalog') }}">
            <svg class="sprite_icon"><use xlink:href="#menu_list"></use></svg>
            <span>Каталог букетов</span>
        </a>
    </li>
    <li><a href="{{ route('reviews') }}">Отзывы</a></li>
    {{-- <li><a href="{{route('blog')}}">Блог</a></li> --}}
    <li><a href="{{ route('zones') }}">Доставка и оплата</a></li>
    <li><a href="{{ route('contacts') }}">Контакты</a></li>
</ul>
