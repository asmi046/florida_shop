@extends('layouts.all')

@php
    $title = $cat_info->title;
    $description = $title."Цветы с доставкой по Курску откомпании Florida";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="category">
    <div class="_wrapper">

        <div class="margin_top_bottom tovar_wrapper">
            @foreach ($allproduct as $tovar)
            <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
            @endforeach
        </div>

    </div>
</section>

@endsection
