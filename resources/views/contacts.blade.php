@extends('layouts.all')

@php
    $title = "Наши контакты";
    $description = "Наши контакты, свяжитесь с нами любым удобным способом";
@endphp

@section('title', $title)
@section('description', $description)

@section('content')

<section class="thencs_page">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title" ></x-breadcrumbs>
        <h1 class="h1_page">{{$title}}</h1>

        <div class="contacts_wrapper">
            <p class="sub_h">Адрес магазина</p>
            <p class="param">{{$options['adress_fk']}}</p>

            <p class="sub_h">Контактный телефон</p>
            <p class="param">{{$options['phone']}}</p>

            <p class="sub_h">Мы в социальных сетях</p>
            <x-messanger></x-messanger>

            <p class="sub_h">Реквизиты</p>
            <div class="text_blk">
                {{$options['organization']}}<br/>
                Юридический адрес: {{$options['adress_ur']}}<br/>
                ИНН: {{$options['inn']}}<br/>
                ОГРН: {{$options['ogrnip']}}<br/>
            </div>
        </div>
    </div>
</section>

@endsection
