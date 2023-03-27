<header id="header">
    <div class="_wrapper">
        <a class="logo" href="{{route('home')}}">
            <img src="{{asset('img/main-logo.svg')}}" alt="Florida - Курск">
        </a>


        <a  href="#" class="addres_head">{{$options['adress_fk']}}</a>



        <x-main-menu></x-main-menu>

        <x-messanger></x-messanger>

        @auth('web')
            <a href="{{route('cabinet.home')}}" class="cabinet">{{ mb_strimwidth(Auth::user()["name"], 0, 20, '...' )}}</a>
        @endauth

        @guest
            <a href="{{route('login')}}" class="cabinet">Войти</a>
        @endguest
    </div>
</header>
