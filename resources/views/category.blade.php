@extends('layouts.all')

@php
    $title = $cat_info->seo_title ?? ($cat_info->showed_title ?? $cat_info->title);
    $description = $cat_info->seo_description ?? $title . ' Цветы с доставкой по Курску откомпании Florida';

    if (!$allproduct->onFirstPage()) {
        $pageSuffix = ' Страница ' . $allproduct->currentPage();
        $title .= $pageSuffix;
        $description .= $pageSuffix;
    }
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

    <x-headers.header-inner :h1="$cat_info->title"></x-headers.header-inner>

    <section class="category">
        <div class="_wrapper">
            @include('partials.product-list')
        </div>
    </section>

    @if ($allproduct->onFirstPage())
        <section class="category">
            <div class="_wrapper">
                <div class="text_styles category_text">
                    {!! $cat_info->description !!}
                </div>
            </div>
        </section>
    @endif

@endsection
