<h1>{{$product['title']}}</h1>


<div class="price_in_page_wrapper">

    <div class="price_in_page">
        <div class="price_ndp">
            Цена:
        </div>

        <div class="price_cerrecter">
            <div class="price">{{$product['price']}} <span class="rub_symbol">₽</span></div>
            @if (!empty($product['old_price']))
                <div class="old_price">{{$product['old_price']}} <span class="rub_symbol">₽</span></div>
            @endif
        </div>
    </div>

    <div class="like_blk">
        <a href="#" onclick="navigator.share({'title': document.title, 'url':document.location.href}); return false;" class="all_control share pi florida_share"></a>
        <a href="#" class="all_control favorites to_favorites pi florida_like" data-prodid="{{$product['sku']}}"></a>
    </div>
</div>

<div class="btn_in_page_wrap">
    <a href="#" class="btn btn_fill card_to_bascet_btn to_bascet" data-prodid="{{$product['sku']}}">
        <span class="nadp">Купить</span>
        <span class="btnLoader"></span>
    </a>

    <div class="quik_pricing_btn_wpap">
        <a href="#showModal" class="btn btn_white">Написать в WhatsApp</a>
        <a href="" class="btn btn_white">Быстрый заказ</a>
    </div>
</div>

<p class="delivery_sens">
    Доставка по Курску: <strong>от 3 000 <span class="rub_symbol">₽</span></strong> - бесплатно
</p>

<div class="pay_sens">
    <span>Принимаем к оплате:</span>
    <img src="{{asset('img/icons/pay_sp.svg')}}" alt="">
</div>

<div class="tov_param_section">
    <h2>Параметры:</h2>
    <div class="param_blk">
        <div class="param param_height pi florida_param_height">
            Высота:<br/>
            <strong>{{$product['height']}}</strong>
        </div>

        <div class="param param_diam pi florida_param_diam">
            Диаметр:<br/>
            <strong>{{$product['radius']}}</strong>
        </div>
    </div>
</div>

<div class="tov_param_section">
    <h2>Состав:</h2>
    <div class="text_blk">
        {!! $product['description'] !!}

    </div>

</div>
