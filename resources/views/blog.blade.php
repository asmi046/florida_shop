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
        <div class="blog_in_page">
            @for ($i = 0; $i<12; $i++)
                <a href="" class="blog_blk">
                    <div class="img_wrp">
                        <img src="{{asset('img/facer_img/tovars/tov_1.jpg')}}" alt="">
                    </div>
                    <h2>Топ - 5 Экзотических цветов в сборных композициях</h2>
                    <p class="data">09.08.2022</p>
                    <p class="descr">1 Эрингиум Оригинальные сборные букеты можно создать именно с этим цветком. Соцветия эрингиума похожи на чертополох, только синего оттенка. Причудливый</p>
                </a>
            @endfor

        </div>

        {{-- <x-pagination :tovars="$reviews"></x-pagination> --}}

    </div>
</section>

@endsection
