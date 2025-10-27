<x-headers.top></x-headers.top>

<header class="header header_inner" id="header">
    <div class="_wrapper">


        <div class="bottom">
            <h1>{!! $h1 !!}</h1>
            <x-breadcrumbs :title="$h1"></x-breadcrumbs>
        </div>
    </div>
    {{--
        <div class="top_line">

            <div class="side left_side">
                <a  href="{{route('contacts')}}" class="addres_head pi florida_map_pin">{{$options['adress_fk']}}</a>
                <x-messanger></x-messanger>
            </div>

            <div class="side left_side">
                @auth('web')
                    <a href="{{route('cabinet.home')}}" class="cabinet pi florida_cabinet">{{ mb_strimwidth(Auth::user()["name"], 0, 20, '...' )}}</a>
                @endauth

                @guest
                    <a href="{{route('login')}}" class="cabinet pi florida_cabinet">Войти</a>
                @endguest
            </div>
        </div> --}}

    {{--
        <div class="_wrapper">
        <a class="logo" href="{{route('home')}}">
            <img src="{{asset('img/logo_main.svg')}}" alt="Florida - Курск">
        </a>

        <x-main-menu></x-main-menu>

        <x-messanger></x-messanger>


    </div> --}}
</header>
