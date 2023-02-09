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
            @foreach ($posts as $item)
                <a href="{{route('blog_page',$item->slug)}}" class="blog_blk">
                    <div class="img_wrp">
                        <img src="{{$item->img}}" alt="">
                    </div>
                    <h2>{{$item->title}}</h2>
                    <p class="data">{{date("d.m.Y", strtotime($item->created_at))}}</p>
                    <p class="descr">{{mb_strimwidth($item->description, 0, 80, '...' )}}</p>
                </a>
            @endforeach

        </div>

        <x-pagination :tovars="$posts"></x-pagination>

    </div>
</section>

@endsection
