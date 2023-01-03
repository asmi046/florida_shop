@extends('layouts.all')

@php
    $title = "Товар";
    $description = "Цветы с доставкой по Курску откомпании Florida";

    $images = [
        [
            'link' => asset('img/facer_img/tovars/tov_1.jpg'),
            'alt' => 'текст текст',
            'title' => 'текст текст',
        ],

        [
            'link' => asset('img/facer_img/tovars/tov_2.jpg'),
            'alt' => 'текст текст',
            'title' => 'текст текст',
        ],

        [
            'link' => asset('img/facer_img/tovars/tov_3.jpg'),
            'alt' => 'текст текст',
            'title' => 'текст текст',
        ],
    ];

    $product = [
        'img' => asset('img/facer_img/tovars/tov_1.jpg'),
        'title' => 'Букет "Гармония"',
        'hit' => "",
        'new' => "",
        'brand' => "Бренд",
        'sku' => "SKU_1",
        'price' => "5000",
        'old_price' => "7000",
        'description' => "Проснувшись однажды утром после беспокойного сна, Грегор Замза обнаружил, что он у себя в постели превратился в страшное насекомое. Лежа на панцирнотвердой спине, он видел, стоило ему приподнять голову, свой коричневый, выпуклый, разделенный дугообразными чешуйками живот, на верхушке которого еле держалось готовое вот-вот окончательно сползти одеяло. Его многочисленные, убого тонкие по сравнению с остальным телом ножки беспомощно копошились у него перед глазами. «Что со мной случилось?» – подумал он."
    ];

@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="tovar_section">
    <div class="_wrapper">
        <x-breadcrumbs :tovar="['title' => $title]" ></x-breadcrumbs>
        <x-tovar-page-content :images="$images" :product="$product"></x-tovar-page-content>
    </div>
</section>

<section id="upsales_section">
    <div class="_wrapper">
        <h2>Похожие товары</h2>
        <div class="tovar_blk_wrap margin_top_bottom">
            @for ($i=0; $i<4; $i++)
                <x-tovar-card></x-tovar-card>
            @endfor
        </div>
    </div>
</section>

@endsection
