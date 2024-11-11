<div class="top">
    <a class="logo" href="{{route('home')}}">
        <img src="{{asset('img/logo_new_white.svg')}}" alt="Florida - Курск">
    </a>

    <x-main-menu></x-main-menu>

    <div class="shop_control">
        <x-icon-a class="search_lnk" href="{{ route('catalog') }}" ancor="Поиск" icon="search"></x-icon-a>

        <x-icon-a class="bascet_lnk" href="{{ route('bascet') }}" ancor="Корзина" icon="cart">
            <bascet-and-counter></bascet-and-counter>
        </x-icon-a>

        @auth('web')
            <x-icon-a class="cabinet_lnk" href="{{route('cabinet.home')}}" ancor="{{ mb_strimwidth(Auth::user()['name'], 0, 20, '...' ) }}" icon="shop"></x-icon-a>
        @endauth

        @guest
            <x-icon-a class="cabinet_lnk" href="{{route('login')}}" ancor="Кабинет" icon="shop"></x-icon-a>
        @endguest


        <x-icon-a class="tel_lnk" href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" ancor="{{ $options['phone'] }}" icon="phone"></x-icon-a>
    </div>
</div>
