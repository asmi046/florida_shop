<header class="header" id="header">
    <div class="_wrapper">
        <x-headers.top></x-headers.top>

        <div class="bottom">
            <x-headers.utp></x-headers.utp>
            <h1>Доставка цветов <br>в Курске</h1>
            <span class="plash">
                Кругласуточно 24/7
            </span>
            <p class="subtitle">Гарантия на цветы — 48 часов <br>или заменим букет!</p>
            <a class="button button_white" href="{{ route('catalog') }}">Выбрать букет</a>
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
