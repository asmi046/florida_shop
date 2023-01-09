@extends('layouts.all')

@php
    $title = "Благодарим за обращение";
    $description = "Цветы с доставкой по Курску откомпании Florida";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <p>Мы свяжемся с Вами в течении 15 минут</p>
    </div>
</section>

@endsection
