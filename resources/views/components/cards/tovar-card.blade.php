
<div data-prodid="{{$tovar['sku']}}" class="tovar_card_new">
    <a href="{{ route('tovar', $tovar->slug) }}" class="img_wrapper">
        <div class="t_label_wrapper">
            @if ($tovar['old_price'])
                <div class="t_label sale">sale</div>
            @endif

            @if ($tovar['hit'])
                <div class="t_label hit">hit</div>
            @endif

            @if ($tovar['new'])
                <div class="t_label new">new</div>
            @endif

        </div>
        <img src="{{ $tovar->img }}" alt="{{ $tovar->title }}">
    </a>
    <p>{{ $tovar->title }}</p>
    <div class="price">
        <div class="price_digit">
            {{ $tovar->price }}₽
            @if ($tovar->old_price)
                <span class="old">
                    {{ $tovar->old_price }}₽
                </span>
            @endif
        </div>

        @if ($tovar->asc_nal)
            <a href="#showModalNal_{{$tovar['sku']}}" class="button price_button">Уточнить наличие</a>
        @else
            <to-bascet-btn-page sku="{{$tovar['sku']}}"  :bascet="'/bascet'"></to-bascet-btn-page>
        @endif
    </div>
</div>

{{--
<div data-prodid="{{$tovar['sku']}}" @class(['sales_tovar_wrapper', 'main-prod-card', 'tovar_wrap','swiper-slide' => $isslide])>
    <div class="bascet_count"> В корзине <span>1</span> шт </div>

    <div class="t_label_wrapper">
        @if ($tovar['old_price'])
            <div class="t_label sale">sale</div>
        @endif

        @if ($tovar['hit'])
            <div class="t_label hit">hit</div>
        @endif

        @if ($tovar['new'])
            <div class="t_label new">new</div>
        @endif

    </div>

    <a href="" data-prodid="{{$tovar['sku']}}" class="favorites pi florida_like like to_favorites"></a>

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

    @if ($tovar->asc_nal)
        <a href="#showModalNal" class="btn btn_in_cart card_to_bascet_btn">Уточнить наличие</a>
    @else

        <to-bascet-btn-page sku="{{$tovar['sku']}}"  :bascet="'/bascet'"></to-bascet-btn-page>
    @endif

</div> --}}

