@php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp

<yml_catalog date="{{ date("Y-m-d\TH:i:sP") }}">
<shop>
    <name>KartaSveta</name>
    <company>ООО "Карта Света"</company>
    <url>https://kartasveta.ru/</url>
    <currencies>
        <currency id="RUR" rate="1"/>
    </currencies>

    <categories>
        @foreach ($all_cat as $item)
            <category id="{{$item->id}}">{{$item->title}}</category>
        @endforeach
    </categories>

    <offers>
        @foreach ($cat_product as $item)
            @php

                if (empty($category)) {
                    $cat = $item->tovar_categories()->first();
                    if (!empty($cat))
                        $cat = $cat["id"];
                    else
                        continue;
                } else {
                    $cat = $category->id;
                }

                $consist = $item->consist;
                if (empty($consist)) continue;
            @endphp

            <offer id="{{$item->sku}}" available="{{($item->insklad > 0)?"true":"false"}}">
                <name>{{$item->title}}</name>
                <url>{{route('tovar', $item->slug)}}</url>

                @if(Storage::disk('local')->exists('public/products_galery/'.$item->img))
                    <picture>{{((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') ."://". $_SERVER['HTTP_HOST']}}{{Storage::url('public/products_galery/'.$item->img)}}</picture>
                @endif

                <picture>{{route('home').$item->img}}</picture>

                <price>{{$item->price}}</price>

                @if (!empty($item->price_old))
                    <oldprice>{{$item->price_old}}</oldprice>
                @endif

                <description>{{$item->description}}</description>
                <currencyId>RUR</currencyId>

                <categoryId>{{ $cat }}</categoryId>

                @foreach ($consist as $elem)
                    <consist name="{{$elem['Имя']}}" unit="шт">{{$elem['Количество']}}</consist>
                @endforeach



                <delivery>true</delivery>
                <store>true</store>
                <pickup>true</pickup>
            </offer>
        @endforeach
    </offers>
    <gifts>
        <!-- подарки не из прайс‑листа -->
    </gifts>
    <promos>
        <!-- промоакции -->
    </promos>
</shop>

</yml_catalog>
