@extends('layouts.all')

@php
    $title = 'Акции на цветы в Курске — Скидки на букеты и розы';
    $description =
        'Ищете цветы со скидкой?  Актуальные акции на букеты, розы и композиции от салона Florida. Выгодные цены и быстрая доставка по Курску.';
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <section class="category">
        <div class="_wrapper">
            <x-breadcrumbs :category="['title' => $title]"></x-breadcrumbs>
            <h1 class="h1_page">Акции и скидки на цветы</h1>

            <div class="margin_top_bottom">
                @foreach ($allproduct as $tovar)
                    <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
                @endforeach
            </div>

        </div>
    </section>

@endsection
