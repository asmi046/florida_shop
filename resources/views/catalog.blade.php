@extends('layouts.all')

@php
    $title = "Каталог букетов в Курске";
    $description = "Самый большой выбор букетов в Курске, доставка букетов по Курску и области.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner h1="Каталог букетов"></x-headers.header-inner>

<x-header-category></x-header-category>

<section id="catalog_section">
    <div class="_wrapper">

        <div class="sitebar">
            <x-tovar-filter></x-tovar-filter>
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
