@extends('layouts.all')

@php
    $rangeTitle = $range['title'];
    $rangeTitleLower = mb_strtolower($rangeTitle);

    $h1 = 'Букеты ' . $rangeTitleLower . ' ₽';
    $title = $h1 . ' — Купить с доставкой в Курске';
    $description = 'Букеты и цветы ' . $rangeTitleLower . ' рублей в салоне Florida. Свежие цветы с доставкой по Курску.';

    if (! $allproduct->onFirstPage()) {
        $pageSuffix = ' Страница ' . $allproduct->currentPage();
        $title .= $pageSuffix;
        $description .= $pageSuffix;
    }
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <x-headers.header-inner :h1="$h1"></x-headers.header-inner>

    <section id="catalog_section">
        <div class="_wrapper">

            <div class="sitebar">
                <x-tovar-filter :price-range="$range"></x-tovar-filter>
            </div>
            <div class="tovars_blk">
                <div class="tovar_wrapper">

                    @foreach ($allproduct as $tovar)
                        <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    <section class="category">
        <div class="_wrapper">
            <x-pagination :tovars="$allproduct"></x-pagination>
        </div>
    </section>

@endsection
