@extends('layouts.all')

@php
    $title = "Инструкция по уходу за букетом";
    $description = "Инструкция по уходу за букетом";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <div class="text_blk">

        </div>
    </div>
</section>

@endsection
