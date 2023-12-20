@extends('layouts.all')

@php
    $title = "Зоны доставки";
    $description = "Зоны доставки цветов по городу Курску и Курской области. Узнай цену цветов с доставкой";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
    </div>
</section>

<x-yandex-delivery></x-yandex-delivery>

<section>
    <div class="_wrapper">
        <div class="text_blk">
            <h2>Способы оплаты</h2>
                <p>
                    <strong>Оплата банковской картой</strong> все заказы в нашем интернет магазине оплачиваются картой на этапе оформления заказа в корзине. Оплата происходит через ПАО СБЕРБАНК с использованием банковских карт следующих платёжных систем:
                </p>

                <div class="pay_sys">
                    <img src="{{asset("img/pay_system/mir.svg")}}" alt="Платежные карты Мир">
                    <img src="{{asset("img/pay_system/visa.svg")}}" alt="Платежные карты Visa">
                </div>
                <div class="pay_sys2">
                    <img src="{{asset("img/pay_system/master.svg")}}" alt="Платежные карты MasterCard">
                    <img src="{{asset("img/pay_system/maestro.svg")}}" alt="Платежные карты Maestro">
                    <img src="{{asset("img/pay_system/JCB.svg")}}" alt="Платежные карты JCB">
                </div>

                <p>
                    <strong>Наличный расчет</strong> осуществляется только при приобретении букетов вточке продаж.
                </p>
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
