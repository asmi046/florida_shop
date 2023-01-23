@extends('layouts.all')

@php
    $title = "Ваша корзина";
    $description = "Ваша корзина";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>

        <div class="main-prod-card tovar_blk_wrap favorites-page tovars_wrap ">

            @foreach ($products as $tovar)
                <x-tovar-card :isslide="false" :tovar="$tovar->tovar_data"></x-tovar-card>
            @endforeach

            <div class="empty_favorites">Жмите на ♡ на странице товара и добавляйте товар в избранное</div>
        </div>

    </div>
</section>

@endsection
