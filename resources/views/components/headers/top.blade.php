<section class="top_header" id="header_top">
    <x-merquee></x-merquee>
    <div class="_wrapper">
        <a class="logo" href="{{ route('home') }}">
            <img src="{{ asset('img/logo_main.svg') }}" alt="Florida - Курск">
        </a>

        <x-main-menu></x-main-menu>

        <x-search-str></x-search-str>

        <div class="mobile_contacts">
            <a title="Свяжитесь с нами в Telegram" href="{{ strip_tags($options['telegram_lnk']) }}"
                class="mobile_contacts__link mobile_contacts__link--telegram">
                <svg class="sprite_icon">
                    <use xlink:href="#tg_icon"></use>
                </svg>
            </a>

            <a title="Свяжитесь с нами в MAX" href="{{ strip_tags($options['whatsapp_lnk']) }}"
                class="mobile_contacts__link mobile_contacts__link--max">
                <svg class="sprite_icon">
                    <use xlink:href="#max_huyax"></use>
                </svg>
            </a>

            <button type="button" title="Поиск" class="mobile_contacts__link mobile_contacts__link--search">
                <svg class="sprite_icon">
                    <use xlink:href="#search"></use>
                </svg>
            </button>
        </div>

        <div class="shop_control">
            {{-- <x-icon-a class="search_lnk" href="{{ route('catalog') }}" ancor="Поиск" icon="search"></x-icon-a> --}}

            <x-icon-a class="bascet_lnk" href="{{ route('bascet') }}" ancor="Корзина" icon="cart">
                <bascet-and-counter></bascet-and-counter>
            </x-icon-a>

            @auth('web')
                <x-icon-a class="cabinet_lnk" href="{{ route('cabinet.home') }}"
                    ancor="{{ mb_strimwidth(Auth::user()['name'], 0, 20, '...') }}" icon="shop"></x-icon-a>
            @endauth

            @guest
                <x-icon-a class="cabinet_lnk" href="{{ route('login') }}" ancor="Кабинет" icon="shop"></x-icon-a>
            @endguest


            <x-icon-a class="tel_lnk" href="tel:{{ str_replace(['-', ' ', '(', ')'], '', $options['phone']) }}"
                ancor="{{ $options['phone'] }}" icon="phone"></x-icon-a>
        </div>
    </div>

    <div class="mobile_search_panel">
        <x-search-str></x-search-str>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchToggle = document.querySelector('.mobile_contacts__link--search');
        const searchPanel = document.querySelector('.mobile_search_panel');

        if (searchToggle && searchPanel) {
            searchToggle.addEventListener('click', function() {
                searchPanel.classList.toggle('mobile_search_panel--open');
                searchToggle.classList.toggle('mobile_contacts__link--search-active');
            });
        }
    });
</script>
