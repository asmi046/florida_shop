@extends('layouts.all')

@php
    $title = "Отзывы наших клиентов";
    $description = "Отзывы клиентов о нашей работе.";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
<x-headers.header-inner :h1="$title"></x-headers.header-inner>

<section class="thencs_page">
    <div class="_wrapper">
        <review></review>
    </div>
</section>



@endsection
