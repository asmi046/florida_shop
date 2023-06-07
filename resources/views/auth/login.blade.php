@extends('layouts.all')

@php
    $title = "Авторизация в личном кабинете"
@endphp

@section('title', $title)
@section('border', "_bottom_border")

@section('content')

<section class="standatr_section">
    <div class="_wrapper">
        <x-breadcrumbs :title="$title"></x-breadcrumbs>

        <h1 class="h1_page">{{$title}}</h1>

        <form class="autch_form" action="{{route('login_do')}}" method="post">
            @csrf

            <input type="text" required name="email" placeholder="Логин">
            @error('email')
                <p class="form_error">{{$message}}</p>
            @enderror

            <input type="password" required name="password" placeholder="Пароль">
            @error('password')
                <p class="form_error">{{$message}}</p>
            @enderror
            <button type="submit" class="btn">Авторизоваться</button>
            <a href="{{route('register')}}">Регистрация</a>
            <a href="{{route('passrec')}}">Забыли пароль?</a>
        </form>
    </div>
</section>

@endsection
