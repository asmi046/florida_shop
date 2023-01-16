@extends('layouts.all')

@section('title', $product['title'])
@section('description', $product['description'])

@section('content')

<section class="tovar_section">
    <div class="_wrapper">
        <x-breadcrumbs :tovar="$product" ></x-breadcrumbs>
        <x-tovar-page-content :images="$images" :product="$product"></x-tovar-page-content>
    </div>
</section>

<section id="upsales_section">
    <div class="_wrapper">
        <h2>Похожие товары</h2>
        <div class="tovar_blk_wrap margin_top_bottom">

            @foreach ($upsale as $tovar)
                <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
            @endforeach
        </div>
    </div>
</section>

@endsection
