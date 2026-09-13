<div class="price_in_page_wrapper">

    <div class="price_in_page">
        <div class="price_ndp">
            Цена:
        </div>

        <div class="price_cerrecter">
            <div class="price">{{ $product['price'] }} <span class="rub_symbol">₽</span></div>
            @if (!empty($product['old_price']))
                <div class="old_price">{{ $product['old_price'] }} <span class="rub_symbol">₽</span></div>
            @endif
        </div>
    </div>

    <div class="like_blk">
        <a href="#"
            onclick="navigator.share({'title': document.title, 'url':document.location.href}); return false;"
            class="all_control share pi florida_share"></a>
        {{-- <a href="#" class="all_control favorites to_favorites pi florida_like" data-prodid="{{$product['sku']}}"></a> --}}
    </div>
</div>

<div class="btn_in_page_wrap">

    @if ($product->asc_nal)
        <a href="#showModalNal" class="button card_to_bascet_btn">Уточнить наличие</a>
    @else
        <to-bascet-btn-page sku="{{ $product['sku'] }}" :bascet="'/bascet'"></to-bascet-btn-page>
    @endif

    <div class="quik_pricing_btn_wpap">
        <a href="https://max.ru/u/f9LHodD0cOJWI1JquUJRpE7xfGtMgxS_TDlt1s0JUfLDqjj7fLt0ZYHiRgE" target="_blank"
            class="button">Написать в Max</a>
        <a href="#ocbuy_{{ $product->id }}" class="button">Быстрый заказ</a>
    </div>
</div>

<div class="pay_sens">
    <span>Принимаем к оплате:</span>
    <img src="{{ asset('img/icons/pay_sp.svg') }}" alt="">
</div>

<div class="tov_param_section">
    <h2>Параметры:</h2>
    <div class="param_blk">
        <div class="param param_height pi florida_param_height">
            Высота:<br />
            <strong>{{ $product['height'] }}</strong>
        </div>

        <div class="param param_diam pi florida_param_diam">
            Диаметр:<br />
            <strong>{{ $product['radius'] }}</strong>
        </div>
    </div>
</div>

@if ($product['consist'] && !empty($product['consist']))
    <div class="tov_param_section">
        <h2>Состав:</h2>
        <div class="text_blk">
            <ul class="consist_list">
                @foreach ($product['consist'] as $item)
                    @continue(empty($item['Имя']))
                    <li>{{ $item['Имя'] }} - {{ $item['Количество'] }} шт.</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if ($product['description'] && !empty($product['description']))
    <div class="tov_param_section">
        <h2>Описание:</h2>
        <div class="text_blk">
            {!! $product['description'] !!}
        </div>
    </div>
@endif

<div class="ahtung">
    <h2>Внимание <span>🛈</span></h2>
    <p>
        Упаковка букета может быть изменена, при этом стилистика и цветовая гамма останутся неизменными.
    </p>
</div>
