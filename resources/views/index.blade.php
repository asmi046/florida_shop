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

    <x-hit-slider></x-hit-slider>

    <x-sales-slider></x-sales-slider>

    <x-catalog-in-main></x-catalog-in-main>

    <x-rew-in-main></x-rew-in-main>

    <x-about-in-main></x-about-in-main>

@endsection
