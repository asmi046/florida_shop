@extends('layouts.all')

@php
    $title = "Согласие на обработку персональных данных";
    $description = "Согласие на обработку персональных данных";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

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
