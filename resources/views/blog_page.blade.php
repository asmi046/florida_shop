@extends('layouts.all')

@php
    $title = $post->seo_title;
    $description = $post->seo_description;
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :blog="$post->title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$post->title}}</h1>

        <div class="page_img_wrapper">
            <img src="{{$post->img}}" alt="{{$post->seo_title}}">
        </div>

        <div class="text_blk">
            {!! $post->description !!}
        </div>
    </div>
</section>

@endsection
