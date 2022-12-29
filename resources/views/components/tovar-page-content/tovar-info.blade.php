<h1>{{$product['title']}}</h1>

<div class="price_in_page_wrapper">
    <div class="price_in_page">
        <div class="price">{{$product['price']}} <span class="rub_symbol">₽</span></div>
        @if (!empty($product['old_price']))
            <div class="old_price">{{$product['old_price']}} <span class="rub_symbol">₽</span></div>
        @endif

    </div>

    <div class="like_blk">
        <a href="#" onclick="navigator.share({'title': document.title, 'url':document.location.href}); return false;" class="all_control share"></a>
        <a href="#" class="all_control favorites to_favorites" data-prodid="{{$product['sku']}}"></a>
    </div>
</div>


<div class="brand_articl_blk">
    <span class="brand">Бренд: {{$product['brand']}}</span>
    <span class="sku">Артикул: {{$product['sku']}}</span>
</div>

<div class="sales_control">
    <div class="labels">
        @if ($product['hit'])
            <div class="label_all label_hit">hit</div>
        @endif

        @if ($product['new'])
            <div class="label_all label_new">new</div>
        @endif

        @if (!empty($product['old_price']))
            <div class="label_all label_procent">%</div>
        @endif
    </div>
</div>


<div class="btn_in_page_wrap">
    <a href="#" class="btn btn_fill card_to_bascet_btn to_bascet" data-prodid="{{$product['sku']}}">
        <span class="nadp">Купить</span>
        <span class="btnLoader"></span>
    </a>
</div>

<div class="tovar_properties_wrap">
    <div class="properti">
        <span class="p_name">Бренд:</span>
        <span class="zap"></span>
        <span class="p_value">{{$product['brand']}}</span>
    </div>

    <div class="properti">
        <span class="p_name">Страна производства:</span>
        <span class="zap"></span>
        <span class="p_value">Индия</span>
    </div>
</div>

@if (false)


<div class="calories_wrap">
    <p>Пищевая ценность на 100 г</p>
    <div class="calories">
        <div class="calories_blk">
            <span>354</span>
            <br>
            ккал
        </div>

        <div class="calories_blk">
            <span>7.8</span>
            <br>
            белки
        </div>

        <div class="calories_blk">
            <span>9.9</span>
            <br>
            жиры
        </div>

        <div class="calories_blk">
            <span>43.9</span>
            <br>
            углеводы
        </div>
    </div>
</div>

@endif

<div class="pay_sens">
    <span>Принимаем к оплате:</span>
    <img src="{{asset('img/icons/pay_sp.svg')}}" alt="">
</div>
