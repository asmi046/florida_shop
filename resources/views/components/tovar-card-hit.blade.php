<div class="swiper-slide">
    <div class="hit_tovar_wrapper">
        <a href="{{route('tovar', $tovar['slug'])}}" class="img_wrapper">
            <img src="{{$tovar['img']}}" alt="{{$tovar['title']}}">
        </a>
        <div class="tov_info">
            <h3><a href="{{route('tovar', $tovar['slug'])}}">{{$tovar['title']}}</a></h3>
            <div class="price">
                {{$tovar['price']}}<span class="rub_symbol">₽</span>

                @if ($tovar['old_price'])
                    <span class="old_price">{{$tovar['old_price']}}<span class="rub_symbol">₽</span></span>
                @endif
            </div>
        </div>

        <div class="btn_cart"></div>
    </div>
</div>
