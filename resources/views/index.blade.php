@extends('layouts.all')

@php
    $title = 'Круглосуточная доставка цветов и букетов в Курске 💐';
    $description =
        'Закажите свежие цветы в Курске с круглосуточной бесплатной доставкой. Букеты на любой повод, быстрая подача, удобная оплата. Работает 24/7.';
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-headers.header></x-headers.header>

    <x-main-category-list></x-main-category-list>

    {{-- <x-header-category></x-header-category> --}}


    <section class="sales_hits_section">
        <div class="_wrapper">
            <h2>Новинки</h2>
            <div class="tovar_wrapper">
                @foreach ($news as $tovar)
                    <x-cards.tovar-card :isslide="true" :tovar="$tovar"></x-cards.tovar-card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sales_hits_section">
        <div class="_wrapper">
            <h2>Хиты продаж</h2>
            <div class="tovar_wrapper">
                @foreach ($hits as $tovar)
                    <x-cards.tovar-card :isslide="true" :tovar="$tovar"></x-cards.tovar-card>
                @endforeach
            </div>
        </div>
    </section>


    <section class="sales_hits_section">
        <div class="_wrapper">
            <h2>Отзывы наших покупателей</h2>
            <review></review>
        </div>
    </section>

    {{-- <x-hit-slider :salesliders="$sales_liders"></x-hit-slider> --}}

    {{-- <x-sales-slider :sales="$sales"></x-sales-slider> --}}

    {{-- <x-catalog-in-main :allproduct="$all_product"></x-catalog-in-main> --}}

    {{-- <x-rew-in-main :reviews="$reviews"></x-rew-in-main> --}}

    {{-- <map-in-page></map-in-page> --}}

    {{-- <x-yandex-delivery></x-yandex-delivery> --}}

    <x-about-in-main></x-about-in-main>





@endsection
