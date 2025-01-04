@extends('layouts.all')

@php
    $title = "Политика в области обработки конфиденциальной информации и персональных данных";
    $description = "Политика в области обработки конфиденциальной информации и персональных данных";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>

        <div class="text_blk">
            {!! $options['policy'] !!}

        </div>
    </div>
</section>

@endsection
