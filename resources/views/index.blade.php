@extends('layouts.all')

@section('title', "Главная страница")

@section('content')
    <x-header></x-header>

    <x-header-control></x-header-control>

    <x-header-category></x-header-category>

    <x-main-banner></x-main-banner>

    <x-advantages></x-advantages>

    <x-hit-slider></x-hit-slider>

    <x-sales-slider></x-sales-slider>

    <x-catalog-in-main></x-catalog-in-main>

    <x-rew-in-main></x-rew-in-main>

    <x-about-in-main></x-about-in-main>

    <x-footer-map></x-footer-map>

    <footer>
        <div class="_wrapper">
            <a class="logo" href="{{route('home')}}">
                <img src="{{asset('img/main-logo.svg')}}" alt="Florida - Курск">
            </a>
            <x-main-menu></x-main-menu>
            <x-messanger></x-messanger>

            <div class="footer_contacts">
                <span class="adress">Курск, ул. Радищева 64</span>
                <a href="" class="phone">+7 (4712) 545 545</a>
            </div>

        </div>
    </footer>
@endsection
