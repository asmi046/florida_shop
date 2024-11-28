@extends('layouts.all')

@php
    $title = "Новинки нашего магазина";
    $description = "Новинки нашего магазина, самые свежие букеты от наших флористов.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="category">

    <div class="_wrapper">
        <x-breadcrumbs :category="['title' => $title]" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>

        <div class="tovar_blk_wrap margin_top_bottom">
            @foreach ($allproduct as $tovar)
                <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
            @endforeach
        </div>

    </div>
</section>

@endsection
