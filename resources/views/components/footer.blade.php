<footer>
    {{-- <div class="_wrapper">
        <a class="logo" href="{{route('home')}}">
            <img src="{{asset('img/logo_main.svg')}}" alt="Florida - Курск">
        </a>
        <x-main-menu></x-main-menu>
        <x-messanger></x-messanger>

        <div class="footer_contacts">
            <span class="adress pi florida_map_pin">{{$options['adress_fk']}}</span>
            <a href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="phone pi florida_phone">{{$options['phone']}}</a>
        </div>
    </div> --}}
    <div class="policy_line">
        <a href="{{route("policy")}}">Политика в области обработки конфиденциальной информации и персональных данных</a>
        <a href="{{route("policy_accept")}}">согласие на обработку персональных данных</a>
    </div>
</footer>
