@extends('layouts.all')

@php
    $title = "Цветы с доставкой по Курску";
    $description = "Цветы с доставкой по Курску откомпании Florida";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-headers.header></x-headers.header>
    <x-header-category></x-header-category>

    {{-- <x-main-banner></x-main-banner>

    <x-advantages></x-advantages> --}}

    <section class="tag_main_section">
        <div class="_wrapper">
            <h2>Возможно Вас заинтересует</h2>
            <x-tegs-main></x-tegs-main>
        </div>
    </section>

    <section class="sales_hits_section">
        <div class="_wrapper">
            <h2>Популярные букеты и Акции</h2>
            <div class="tovar_wrapper">
                @foreach ($sales as $tovar)
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
