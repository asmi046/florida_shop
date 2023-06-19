@extends('layouts.all')

@php
    $title = $cat_info->title;
    $description = $title."Цветы с доставкой по Курску откомпании Florida";
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
