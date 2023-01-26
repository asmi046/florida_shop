@extends('layouts.all')

@php
    $title = "Мои заказы"
@endphp

@section('title', $title)
@section('border', "_bottom_border")

@section('content')



<section class="standatr_section section_minheight" >
    <div class="_wrapper">
        <x-breadcrumbs :title="$title"></x-breadcrumbs>

        @include('cabinet.cabinet-control-panel')

        <div class="cabinet_orders_wrapper">
            @foreach ($orders as $elem)
                <div class="cabinet_order">
                    <h2>Заказ# {{$elem['id']}}</h2>
                    <span class="zak_data">Оформлен: {{date("d.m.Y H:i:s", strtotime($elem['created_at']))}}</span>
                    <div class="tovat_in_zak_wrapper">
                        @foreach ($elem['orderProducts'] as $order_tovar)
                            <a href="{{route('tovar', $order_tovar['slug'])}}" class="tovat_in_zak_blk">
                                @if ($order_tovar['img'] =="")
                                    <img src="{{asset('img/noPhoto.jpg')}}" alt="{{$order_tovar['title']}}">
                                @else
                                    <img src="{{$order_tovar['img']}}" alt="{{$order_tovar['title']}}">
                                @endif

                                <p>{{$order_tovar['title']}}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>


@endsection
