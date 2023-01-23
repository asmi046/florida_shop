<!DOCTYPE html>
<html lang="ru">
<head>
    <title>@yield('title') - Florida</title>
    <meta name="description" content="@yield('description')">

    <meta property="og:locale" content="ru_RU" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title') />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:url" content="{{route('home')}}" />
    <meta property="og:site_name" content="Магазин индийских товаров - Mini India" />
    <meta property="og:image" content="{{asset('img/og_img.jpg')}}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta name="twitter:card" content="summary_large_image" />

	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">

    <link rel="icon" type="image/png" href="{{asset('/img/favicons/icon256.png')}}" sizes="256x256">
    <link rel="icon" type="image/png" href="{{asset('/img/favicons/icon128.png')}}" sizes="128x128">
    <link rel="icon" type="image/png" href="{{asset('/img/favicons/icon64.png')}}" sizes="64x64">
    <link rel="icon" type="image/png" href="{{asset('/img/favicons/icon32.png')}}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{asset('/img/favicons/icon16.png')}}" sizes="16x16">
    <link rel="icon" type="image/svg" href="{{asset('/img/favicons/logo-mini.svg')}}" sizes="any">

    <meta name="_token" content="{{ csrf_token() }}">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{asset('js/lib/swiper/swiper-bundle.min.css')}}"/>
    <script src="{{asset('js/lib/swiper/swiper-bundle.min.js')}}"></script>

    <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU&coordorder=longlat&apikey=c38cf20b-9826-491c-abe0-0653932a0e17" type="text/javascript"></script>

    @vite([
        'resources/css/app.css',
        'public/css/main.css',
        'public/css/tovar_filter.css',
        'public/css/tovar_page_content.css',
        'public/css/catalog_menu.css',
        'public/css/mobile_bottom_menu.css',
        'public/css/cart.css',


        'resources/js/app.js',
        'public/js/sliders.js',
        'public/js/delivery_zone.js',
        'public/js/filter.js',
        'public/js/map.js',
        'public/js/categories.js',
        'public/js/catalog_menu.js',
        'public/js/cart.js',
        'public/js/favorites.js'
    ])

</head>

<body>
    <x-catalog-menu></x-catalog-menu>
    <x-mobile-bottom-menu></x-mobile-bottom-menu>



	<div class="wrapper" id="global_app">

        <modal-window rout="/send_consult" redirect="/thencs" hesh="showModal" title="Помощь флориста" subtitle="Мы свяжемся с Вами в течении 15 минут"></modal-window>

        <x-header></x-header>
        <x-header-control></x-header-control>
        <x-header-category></x-header-category>

        @yield('content')

        <x-footer-map></x-footer-map>
        <x-footer></x-footer>
    </div>
</body>

</html>
