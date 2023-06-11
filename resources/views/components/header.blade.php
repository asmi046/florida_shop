<header id="header">
    <div class="_wrapper">
        <a class="logo" href="{{route('home')}}">
            <img src="{{asset('img/logo_main.svg')}}" alt="Florida - Курск">
        </a>


        <a  href="{{route('contacts')}}" class="addres_head pi florida_map_pin">{{$options['adress_fk']}}</a>


        <x-main-menu></x-main-menu>

        <x-messanger></x-messanger>

        @auth('web')
            <a href="{{route('cabinet.home')}}" class="cabinet pi florida_cabinet">{{ mb_strimwidth(Auth::user()["name"], 0, 20, '...' )}}</a>
        @endauth

        @guest
            <a href="{{route('login')}}" class="cabinet pi florida_cabinet">Войти</a>
        @endguest
    </div>
</header>
