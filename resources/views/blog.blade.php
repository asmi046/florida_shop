@extends('layouts.all')

@php
    $title = "Наш блог";
    $description = "Наш блог. Все о цветах и букетах, флористике и не только.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="thencs_page">
    <div class="_wrapper">
        <div class="blog_in_page">
            @foreach ($posts as $item)
                <x-blog-card :item="$item" />
            @endforeach

        </div>

        <x-pagination :tovars="$posts"></x-pagination>

    </div>
</section>

@endsection
