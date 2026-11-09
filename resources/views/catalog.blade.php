@extends('layouts.all')

@php
    $title = 'Каталог цветов — Купить букет в Курске с доставкой';
    $description =
        'Полный каталог цветов магазина Florida.  Розы, пионы, хризантемы, сборные букеты и композиции в коробках. Выбирайте и заказывайте онлайн!';
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <x-headers.header-inner h1="Каталог цветов и букетов"></x-headers.header-inner>

    <x-header-category></x-header-category>

    <section id="catalog_section">
        <div class="_wrapper">

            <div class="sitebar">
                <x-tovar-filter></x-tovar-filter>
            </div>
            <div class="tovars_blk">

                @include('partials.product-list')

            </div>


        </div>
    </section>

@endsection
