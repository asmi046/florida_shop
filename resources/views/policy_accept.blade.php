@extends('layouts.all')

@php
    $title = "Согласие на обработку персональных данных";
    $description = "Согласие на обработку персональных данных";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')
<x-headers.header-inner :h1="$title"></x-headers.header-inner>
<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <div class="text_blk">
            {!! $options['policy_accept'] !!}

        </div>
    </div>
</section>

@endsection
