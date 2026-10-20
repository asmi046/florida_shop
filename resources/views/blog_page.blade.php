@extends('layouts.all')

@php
    $title = $post->title;
    $description = $post->seo_description;
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

@php
    $blogSchemaJson = json_encode(
        [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->short_description ?: $post->seo_description,
            'image' => asset($post->img),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog_page', $post->slug),
            ],
        ],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );
@endphp
@jsonld(jsonld_raw($blogSchemaJson))

<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="thencs_page">
    <div class="_wrapper">

        <div class="page_img_wrapper">
            <img src="{{$post->img}}" alt="{{$post->seo_title}}">
        </div>

        <div class="text_blk blog_article">
            {!! $post->description !!}
        </div>
    </div>
</section>

@endsection
