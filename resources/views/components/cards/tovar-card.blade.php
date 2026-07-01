<div data-prodid="{{ $tovar['sku'] }}" class="tovar_card_new">
    <a href="{{ route('tovar', $tovar->slug) }}" @class([
        'img_wrapper',
        'has-second' => $tovar->product_images && count($tovar->product_images) > 0,
    ])>
        <div class="t_label_wrapper">
            @if ($tovar['old_price'])
                <div class="t_label sale">распродажа</div>
            @endif

            @if ($tovar['hit'])
                <div class="t_label hit">хит</div>
            @endif

            @if ($tovar['new'])
                <div class="t_label new">новинка</div>
            @endif

        </div>
        <img class="tovar_img tovar_img_first" loading="lazy" src="{{ $tovar->img }}" alt="{{ $tovar->title }}">
        @if ($tovar->product_images && count($tovar->product_images) > 0)
            <img class="tovar_img tovar_img_second" loading="lazy" src="{{ $tovar->product_images[0]->link }}"
                alt="{{ $tovar->title }}">
        @endif
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
            <a href="#showModalNal_{{ $tovar['sku'] }}" class="button price_button">Уточнить наличие</a>
        @else
            <to-bascet-btn-page sku="{{ $tovar['sku'] }}" :bascet="'/bascet'"></to-bascet-btn-page>
        @endif
    </div>
</div>
