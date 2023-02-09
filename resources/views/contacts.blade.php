@extends('layouts.all')

@php
    $title = "Наши контакты";
    $description = "Наши контакты, свяжитесь с нами любым удобным способом";
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
