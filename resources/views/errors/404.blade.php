@extends('layouts.all')

@section('title', 'Страница не найдена - 404')
@section('description', 'Запрашиваемая страница не найдена')

@section('content')

<x-headers.header-inner h1="Страница не найдена - 404"></x-headers.header-inner>

<section class="error-page">
    <div class="_wrapper">
        <div class="error-content">
            <p class="error-description">
                К сожалению, запрашиваемая вами страница не существует или была удалена.
            </p>
            <br>
            <div class="error-actions">
                <a href="{{ route('home') }}" class="button button_green">
                    На главную
                </a>
                <a href="{{ route('catalog') }}" class="button ">
                    В каталог
                </a>
            </div>
        </div>


    </div>
</section>

<x-header-category></x-header-category>

@endsection
