@extends('layouts.all')

@php
    $title = "Регистрация в личном кабинете"
@endphp

@section('title', $title)
@section('border', "_bottom_border")

@section('content')



<section class="standatr_section">
    <div class="_wrapper">
            <x-breadcrumbs :title="$title"></x-breadcrumbs>

            <h1 class="h1_page">{{$title}}</h1>

            <form class="autch_form" action="{{route('register_do')}}" method="post">
                @csrf

                <input name="phone" required type="tel" placeholder="Телефон*">
                @error('phone')
                    <p class="form_error">{{$message}}</p>
                @enderror

                <input name="email" required type="text" placeholder="e-mail*">
                @error('email')
                    <p class="form_error">{{$message}}</p>
                @enderror

                <input name="name" required type="text" placeholder="Имя*">
                @error('name')
                    <p class="form_error">{{$message}}</p>
                @enderror

                <input name="password" required type="password"  placeholder="Пароль*">
                @error('password')
                    <p class="form_error">{{$message}}</p>
                @enderror

                <input name="password_confirmation" required type="password"  placeholder="Повторите пароль*">
                @error('password_confirmation')
                    <p class="form_error">{{$message}}</p>
                @enderror
                <button type="submit" class="btn">Регистрация</button>
                <a href="{{route('login')}}">Уже есть аккаунт?</a>
            </form>

    </div>
</section>



@endsection
