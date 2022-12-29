@extends('layouts.all')

@php
    $title = "Категория";
    $description = "Цветы с доставкой по Курску откомпании Florida";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="category">
    <div class="_wrapper">
        <x-breadcrumbs :category="['title' => $title]" ></x-breadcrumbs>
        <h1 class="h1_page">Категория</h1>

        <div class="tovar_blk_wrap margin_top_bottom">
            @for ($i=0; $i<20; $i++)
                <x-tovar-card></x-tovar-card>
            @endfor
        </div>

    </div>
</section>

@endsection
