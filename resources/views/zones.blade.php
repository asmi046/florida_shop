@extends('layouts.all')

@php
    $title = "Способы оплаты и доставки";
    $description = "Зоны доставки цветов по городу Курску и Курской области. Узнай цену цветов с доставкой";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<x-headers.header-inner :h1="$title"></x-headers.header-inner>


{{-- <x-yandex-delivery></x-yandex-delivery> --}}

<section>
    <div class="_wrapper">
        <div class="text_blk">
            <h2>Доставка</h2>
                    <p class="free"><strong>Бесплатная доставка</strong> распространяется на: проспект Победы, Дериглазова, ул. Карла-Маркса</p>
                    <p class="pay">Доставка в другие районы: 150-250 р.</p>
                    <p class="pay">Доставка в ко времени: 500 р.</p>
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
