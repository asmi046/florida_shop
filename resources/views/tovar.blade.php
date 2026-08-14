@extends('layouts.all')

@section('title', "Купить {$product['title']} в Курске за {$product['price']} руб.")
@section('description', "Заказывайте {$product['title']} с доставкой по Курску! Свежие цветы, круглосуточная доставка, фото перед отправкой. Цена: {$product['price']} руб. Салон Florida.")
@section('img', config('app.url').$product['img'])

@section('content')

<tovar-data-send :tovardata="{{ json_encode($product) }}"></tovar-data-send>

<x-headers.header-inner h1="{{ $product['title'] }}"></x-headers.header-inner>

<section class="tovar_section">
    <div class="_wrapper">
        <x-tovar-page-content :images="$images" :product="$product"></x-tovar-page-content>
    </div>
</section>

<section id="upsales_section">
    <div class="_wrapper">
        <h2>Похожие товары</h2>
        <div class="margin_top_bottom tovar_wrapper">

            @foreach ($upsale as $tovar)
                <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
            @endforeach
        </div>
    </div>
</section>

@endsection
