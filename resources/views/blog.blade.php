@extends('layouts.all')

@php
    $title = "Наш блог";
    $description = "Наш блог. Все о цветах и букетах, флористике и не только.";
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
