<header class="header" id="header">
    <div class="_wrapper">
        <div class="top">
            <a class="logo" href="{{route('home')}}">
                <img src="{{asset('img/logo_new_white.svg')}}" alt="Florida - Курск">
            </a>

            <x-main-menu></x-main-menu>

            <div class="shop_control">
                <x-icon-a href="#" ancor="Поиск" icon="search"></x-icon-a>
                <x-icon-a href="#" ancor="Корзина" icon="cart"></x-icon-a>
                <x-icon-a href="#" ancor="Кабинет" icon="shop"></x-icon-a>
            </div>
        </div>

        <div class="bottom">
            <h1>Доставка цветов <br>в Курске</h1>
            <span class="plash">
                Кругласуточно 24/7
            </span>
            <p>Гарантия на цветы — 48 часов <br>или заменим букет!</p>
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
