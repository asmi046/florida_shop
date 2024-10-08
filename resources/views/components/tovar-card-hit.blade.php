<div class="swiper-slide">
    <a href="{{route('tovar', $tovar['slug'])}}" class="hit_tovar_wrapper">
        <div class="img_wrapper">
            <img src="{{$tovar['img']}}" alt="{{$tovar['title']}}">
        </div>
        <div class="tov_info">
            <h3>{{$tovar['title']}}</h3>
            <div class="price">
                {{$tovar['price']}}<span class="rub_symbol">₽</span>

                @if ($tovar['old_price'])
                    <span class="old_price">{{$tovar['old_price']}}<span class="rub_symbol">₽</span></span>
                @endif
            </div>
        </div>

        <div class="btn_cart"></div>
    </a>
</div>
