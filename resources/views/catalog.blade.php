@extends('layouts.all')

@php
    $title = "Каталог букетов в Курске";
    $description = "Самый большой выбор букетов в Курске, доставка букетов по Курску и области.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="category">
    <div class="_wrapper">
        <h1 class="h1_page">Каталог букетов</h1>
    </div>
</section>

<section id="catalog_section">
    <div class="_wrapper">

        <div class="sitebar">
            <x-tovar-filter></x-tovar-filter>
        </div>
        <div class="tovars_blk">
            <div class="tovar_blk_wrap">

                @foreach ($allproduct as $tovar)
                    <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
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
