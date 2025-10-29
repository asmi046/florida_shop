<div class="catalog_menu_wrapper_zt">
	<div class="close_catalog"></div>
</div>
<div class="catalog_menu_wrapper">
	<div class="catalog_menu">
        <h2>Каталог</h2>

        <x-search-str class="in_menu"></x-search-str>

        <div class="catclog_razdel_wrapper">
            <a href="{{ route('zones') }}">Доставка и оплата</a>
            <div class="cat_razdel">
                <h3>Категории</h3>
                <ul>
                    @foreach ($all_cat as $cat)
                        <li><a href="{{route('category', $cat->slug)}}">{{$cat->title}}</a></li>
                    @endforeach

                </ul>
            </div>

            <div class="cat_razdel">
                <h3>Праздник</h3>
                <ul>
                    @foreach ($celebrations as $celebration)
                        <li><a href="{{route('celebration', $celebration->slug)}}">{{$celebration->title}}</a></li>
                    @endforeach

                </ul>
            </div>

            <div class="cat_razdel">
                <h3>Особенности</h3>
                <ul>
                    <li><a href="{{route('hits')}}">Хит продаж</a></li>
                    <li><a href="{{route('new_tovar')}}">Новинка</a></li>
                    <li><a href="{{route('actions')}}">Скидки</a></li>
                </ul>
            </div>
        </div>
	</div>
</div>
