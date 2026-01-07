<x-headers.top></x-headers.top>

<header class="header" id="header">


    <div class="header-slider">
        <div class="swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="slide-background" style="background-image: url('{{ asset('img/banner_0_1.webp') }}')">
                    </div>
                    <div class="slide-overlay"></div>
                    <x-headers.reiting></x-headers.reiting>
                    <div class="slide-content">
                        <h1 class="slide-title">Доставка цветов в Курске</h1>
                        <p class="slide-subtitle">Круглосуточно 24/7! Гарантия на цветы — 48 часов или заменим букет!
                        </p>
                        <a href="{{ route('catalog') }}" class="slide-button">Заказать букет</a>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="slide-background" style="background-image: url('{{ asset('img/banner_0_2.webp') }}')">
                    </div>
                    <div class="slide-overlay"></div>
                    <x-headers.reiting></x-headers.reiting>
                    <div class="slide-content">
                        <h2 class="slide-title">Букеты премиум качества в Курске</h2>
                        <p class="slide-subtitle">Каждый день для Вас только отборные цветы и композиции от лучших
                            флористов города.</p>
                        <a href="{{ route('catalog') }}" class="slide-button">Все букеты</a>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>

    {{-- <div class="_wrapper">


        <div class="bottom">
            <x-headers.utp></x-headers.utp>
            <h1>Доставка цветов <br>в Курске</h1>
            <span class="plash">
                Круглосуточно 24/7
            </span>
            <p class="subtitle">Гарантия на цветы — 48 часов <br>или заменим букет!</p>
            <a class="button button_white" href="{{ route('catalog') }}">Каталог</a>
        </div>
    </div> --}}

</header>
