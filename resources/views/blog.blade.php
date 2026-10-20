@extends('layouts.all')

@php
    $title = "Наш блог";
    $description = "Наш блог. Все о цветах и букетах, флористике и не только.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

@php
    $blogItems = [];
    $position = $posts->firstItem() ?? 1;

    foreach ($posts as $blogPost) {
        $blogItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'url' => route('blog_page', $blogPost->slug),
            'name' => $blogPost->title,
        ];
    }

    $blogSchemaJson = json_encode(
        [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'url' => route('blog'),
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $blogItems,
            ],
        ],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );
@endphp
@jsonld(jsonld_raw($blogSchemaJson))

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
