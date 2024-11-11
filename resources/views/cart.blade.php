@extends('layouts.all')

@php
    $title = "Ваша корзина";
    $description = "Ваша корзина";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="thencs_page">
    <div class="_wrapper">
        {{-- <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1> --}}
        <bascet></bascet>
    </div>
</section>

@endsection
