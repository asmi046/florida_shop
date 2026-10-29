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

            <div class="margin_top_bottom tovar_wrapper">
                @foreach ($allproduct as $tovar)
                    <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
                @endforeach
            </div>

        </div>
    </section>

@endsection
