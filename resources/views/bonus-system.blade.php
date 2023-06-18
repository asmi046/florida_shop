@extends('layouts.all')

@php
    $title = "Бонусная программа нашего магазина";
    $description = "Бонусная программа нашего магазина. Покупайте букеты с выгодой, накаплива";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <div class="text_blk">

            @if (isset($options['bonus-system']))
                {!! $options['bonus-system'] !!}
            @else
                <h2>Раздел в разработке информация скоро появится...</h2>
            @endif

        </div>
    </div>
</section>


<x-advantages></x-advantages>

<x-hit-slider :salesliders="$sales_liders"></x-hit-slider>

<map-in-page></map-in-page>


@endsection
