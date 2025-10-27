@extends('layouts.all')

@section('title', $tag_info['seo_title'])
@section('description', $tag_info['seo_description'])

@section('content')

<x-headers.header-inner :h1="$tag_info['alt_title']"></x-headers.header-inner>

<section id="catalog_section" class="catalog_section">
    <div class="_wrapper">
         <div class="sitebar">
            <x-tovar-filter></x-tovar-filter>
        </div>
        <div class="tovars_blk">
            <div class="tovar_wrapper">

                @foreach ($allproduct as $tovar)
                    <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
                @endforeach
            </div>
        </div>
    </div>
</section>
<section class="category">
    <div class="_wrapper">
        <x-pagination :tovars="$allproduct"></x-pagination>
    </div>
</section>

@endsection
