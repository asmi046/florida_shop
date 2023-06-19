@extends('layouts.all')

@php
    $title = "Бонусная система - Личный кабинет"
@endphp

@section('title', $title)
@section('border', "_bottom_border")

@section('content')

<section class="standatr_section section_minheight" >
    <div class="_wrapper">

        <x-breadcrumbs :title="$title"></x-breadcrumbs>

        @include('cabinet.cabinet-control-panel')

        <div class="cabinet_content_panel">
            <div class="bonus_sus_wrapper">
                <h2>Вам начисленно<br/> <b>{{$bonuses_count}}</b> бала</h2>
            </div>
        </div>

    </div>
</section>


@endsection
