@extends('layouts.all')

@php
    $title = "Акции и скики нашего магазина";
    $description = "Акции и скики нашего магазина, мы предлагаем самые выгодные цены на букеты в Курске";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="category">
    <div class="_wrapper">
        <x-breadcrumbs :category="['title' => $title]" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>

        <div class="margin_top_bottom">
            @foreach ($allproduct as $tovar)
                <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
            @endforeach
        </div>

    </div>
</section>

@endsection
