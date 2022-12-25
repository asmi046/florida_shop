@extends('layouts.all')

@section('title', "Главная страница")

@section('content')
    <x-header></x-header>

    <x-header-control></x-header-control>

    <x-header-category></x-header-category>

    <x-main-banner></x-main-banner>

    <x-advantages></x-advantages>

    <section class="hit_sliders">
        <div class="_wrapper">
            <div class="swiper_obj">
                <div class="swiper hit_slider">
                    <div class="swiper-wrapper">

                        @for ($i=0; $i<10; $i++)
                            <div class="swiper-slide">
                                <div class="hit_tovar_wrapper">
                                    <div class="img_wrapper">
                                        <img src="{{asset('img/tovar_min.jpg')}}" alt="">
                                    </div>
                                    <div class="tov_info">
                                        <h3>Композиция "Оттенок нежности"</h3>
                                        <div class="price">
                                            2105<span class="rub_symbol">₽</span> <span class="old_price">3000<span class="rub_symbol">₽</span></span>
                                        </div>
                                    </div>

                                    <div class="btn_cart"></div>
                                </div>
                            </div>
                        @endfor

                    </div>
                </div>

                <div id="hit_btn_left" class="btn_all btn_left"></div>
                <div id="hit_btn_right" class="btn_all btn_right"></div>
            </div>
        </div>
    </section>

@endsection
