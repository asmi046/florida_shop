@extends('layouts.all')

@php
    $title = "Цветы с доставкой по Курску";
    $description = "Цветы с доставкой по Курску откомпании Florida";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <x-main-banner></x-main-banner>

    <x-advantages></x-advantages>

    <x-hit-slider :salesliders="$sales_liders"></x-hit-slider>

    <x-sales-slider :sales="$sales"></x-sales-slider>

    <x-catalog-in-main :allproduct="$all_product"></x-catalog-in-main>

    <x-rew-in-main :reviews="$reviews"></x-rew-in-main>

    <map-in-page></map-in-page>

    <x-about-in-main></x-about-in-main>

@endsection
