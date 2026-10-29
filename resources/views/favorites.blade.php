@extends('layouts.all')

@php
    $title = "Избранное";
    $description = "Понравившиеся товары (Избранное букеты Florida46)";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>

        <div class="favorites-page tovar_wrapper ">

            @foreach ($products as $tovar)
                <x-cards.tovar-card :tovar="$tovar->tovar_data"></x-cards.tovar-card>
            @endforeach

            <div class="empty_favorites">Жмите на ♡ на странице товара и добавляйте товар в избранное</div>
        </div>

    </div>
</section>

@endsection
