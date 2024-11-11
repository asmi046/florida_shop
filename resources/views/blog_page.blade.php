@extends('layouts.all')

@php
    $title = $post->title;
    $description = $post->seo_description;
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="thencs_page">
    <div class="_wrapper">

        <div class="page_img_wrapper">
            <img src="{{$post->img}}" alt="{{$post->seo_title}}">
        </div>

        <div class="text_blk">
            {!! $post->description !!}
        </div>
    </div>
</section>

@endsection
