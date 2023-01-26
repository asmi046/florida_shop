@extends('layouts.all')

@php
    $title = "Личьный кабинет"
@endphp

@section('title', $title)
@section('border', "_bottom_border")

@section('content')

<section class="standatr_section section_minheight" >
    <div class="_wrapper">

        <x-breadcrumbs :title="$title"></x-breadcrumbs>

        @include('cabinet.cabinet-control-panel')

        <div class="cabinet_content_panel">
            <form class="autch_form" action="{{route('save_user_data')}}" method="post">
                @csrf

                <input type="hidden" name="email" value="{{Auth::user()["email"]}}">

                <input name="email_w" disabled required value="{{Auth::user()["email"]}}" required type="text" placeholder="e-mail*">
                @error('email')
                    <p class="form_error">{{$message}}</p>
                @enderror

                <input name="name" value="{{Auth::user()["name"]}}" required type="text" placeholder="Имя*">
                @error('name')
                    <p class="form_error">{{$message}}</p>
                @enderror

                {{--
                <input name="password" required type="password"  placeholder="Пароль*">
                @error('password')
                    <p class="form_error">{{$message}}</p>
                @enderror

                <input name="password_confirmation" required type="password"  placeholder="Повторите пароль*">
                @error('password_confirmation')
                    <p class="form_error">{{$message}}</p>
                @enderror --}}

                <button type="submit" class="btn">Сохранить данные</button>
            </form>
        </div>




    </div>
</section>


@endsection
