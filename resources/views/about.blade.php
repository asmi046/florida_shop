@extends('layouts.all')

@php
    $title = "Лучший магазин цветов в Курске";
    $description = "Лучший магазин цветов в Курске - Florida46! Доставка цветов по Курску и области. Бонусы акции и скидки.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <div class="text_blk">

            @if (isset($options['about']))
                {!! $options['about'] !!}
            @else
                <h2>Раздел в разработке информация скоро появится...</h2>
            @endif

        </div>
    </div>
</section>

@endsection
