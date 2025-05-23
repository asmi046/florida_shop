@extends('layouts.all')

@php
    $title = "Оплата и доставка";
    $description = "Информация о условиях оплаты и доставки";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>
        <div class="text_blk">
            <h2>Способы оплаты</h2>
            <p>Мы осуществляем </p>
            <h2>Возврат</h2>
            <p>
                Возврт букетов и живых цветов осуществляется в течение 24 часов с момента покупки и исключительно в случае доставки товара ненадлежащего качества. При приобретении указанных выше товаров в точке продаж обмен и возврат не осуществлется.
            </p>

            <p>
                Возврат переведённых средств, производится на ваш банковский счёт в течение 5-30 рабочих дней (срок зависит от банка, который выдал вашу банковскую карту).
            </p>

        </div>
    </div>
</section>

@endsection
