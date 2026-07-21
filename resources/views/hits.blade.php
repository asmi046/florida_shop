@extends('layouts.all')

@php
    $title = 'Популярные букеты цветов — Хиты продаж в Курске';
    $description =
        'Самые заказываемые букеты в салоне Florida.  Выбор наших клиентов: популярные розы, сборные композиции. Проверенное качество и восторг получателя!';
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <section class="category">
        <div class="_wrapper">
            <x-breadcrumbs :category="['title' => $title]"></x-breadcrumbs>
            <h1 class="h1_page">Хиты продаж: популярные букеты</h1>

            <div class="tovar_blk_wrap margin_top_bottom">
                @foreach ($allproduct as $tovar)
                    <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
                @endforeach
            </div>

        </div>
    </section>

@endsection
