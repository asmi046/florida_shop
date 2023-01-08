<div class="catalog_menu_wrapper_zt">
	<div class="close_catalog"></div>
</div>
<div class="catalog_menu_wrapper">
	<div class="catalog_menu">
        <h2>Каталог</h2>
        <div class="catclog_razdel_wrapper">
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
                        <li><a href="{{route('category', $celebration->slug)}}">{{$celebration->title}}</a></li>
                    @endforeach

                </ul>
            </div>

            <div class="cat_razdel">
                <h3>Особенности</h3>
                <ul>
                    <li><a href="#">Хит продаж</a></li>
                    <li><a href="#">Новинка</a></li>
                    <li><a href="#">Скидки</a></li>
                </ul>
            </div>
        </div>
	</div>
</div>
