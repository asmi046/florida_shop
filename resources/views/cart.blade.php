@extends('layouts.all')

@php
    $title = "Ваша корзина";
    $description = "Ваша корзина";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <bascet></bascet>
    </div>
</section>

@endsection
