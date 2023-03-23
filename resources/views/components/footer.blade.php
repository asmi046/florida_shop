<footer>
    <div class="_wrapper">
        <a class="logo" href="{{route('home')}}">
            <img src="{{asset('img/main-logo.svg')}}" alt="Florida - Курск">
        </a>
        <x-main-menu></x-main-menu>
        <x-messanger></x-messanger>

        <div class="footer_contacts">
            <span class="adress">{{$options['adress']}}</span>
            <a href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="phone">{{$options['phone']}}</a>
        </div>

    </div>
    <div class="policy_line">
        <a href="{{route("policy")}}">Политика в области обработки конфиденциальной информации и персональных данных</a>
    </div>
</footer>
