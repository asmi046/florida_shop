@extends('layouts.all')

@php
    $title = 'Отзывы о доставке цветов магазина Florida в Курске';
    $description =
        'Реальные отзывы клиентов о покупке и доставке цветов в магазине Florida.  Читайте впечатления о наших букетах и работе флористов.';
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
    <x-headers.header-inner :h1="'Отзывы наших клиентов'"></x-headers.header-inner>

    <section class="thencs_page">
        <div class="_wrapper">
            <review></review>
        </div>
    </section>



@endsection
