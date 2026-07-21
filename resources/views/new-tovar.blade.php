@extends('layouts.all')

@php
    $title = 'Новинки букетов — Свежие поступления цветов в Курске';
    $description =
        'Новые коллекции букетов от флористов салона Florida.  Современные тренды, свежие поставки экзотических цветов и авторские композиции.';
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <section class="category">

        <div class="_wrapper">
            <x-breadcrumbs :category="['title' => $title]"></x-breadcrumbs>
            <h1 class="h1_page">Новинки флористики</h1>

            <div class="tovar_blk_wrap margin_top_bottom">
                @foreach ($allproduct as $tovar)
                    <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
                @endforeach
            </div>

        </div>
    </section>

@endsection
