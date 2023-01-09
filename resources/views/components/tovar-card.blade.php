
<div @class(['sales_tovar_wrapper', 'swiper-slide' => $isslide])>
    <div class="tovar_label_wrapper">
        @if ($tovar['old_price'])
            <div class="tovar_label sale">sale</div>
        @endif

        @if ($tovar['hit'])
            <div class="tovar_label hit">hit</div>
        @endif

        @if ($tovar['new'])
            <div class="tovar_label new">new</div>
        @endif

    </div>

    <a href="" class="favorites"></a>

    <a href="{{route('tovar', $tovar['slug'])}}" class="img_wrapper">
        <img src="{{$tovar['img']}}" alt="{{$tovar['title']}}<">
    </a>
    <div class="tov_info">
        <div class="price">
            <span class="true_price">{{$tovar['price']}}<span class="rub_symbol">₽</span></span>

            @if ($tovar['old_price'])
                <span class="old_price">{{$tovar['old_price']}}<span class="rub_symbol">₽</span></span>
            @endif

        </div>
        <h3><a href="{{route('tovar', $tovar['slug'])}}">{{$tovar['title']}}</a></h3>
    </div>
    <a href="" class="btn btn_in_cart">Купить</a>
</div>

