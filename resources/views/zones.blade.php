@extends('layouts.all')

@php
    $title = "Зоны доставки";
    $description = "Зоны доставки цветов по городу Курску и Курской области. Узнай цену цветов с доставкой";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>

    </div>
</section>

@endsection
