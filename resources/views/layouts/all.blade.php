<!DOCTYPE html>
<html lang="ru">
<head>
    <title>@yield('title') - Florida</title>
    <meta name="description" content="@yield('description')">

    <meta property="og:locale" content="ru_RU" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:url" content="{{route('home')}}" />
    <meta property="og:site_name" content="Магазин цветов Florida 46" />
    <meta property="og:image" content="@yield('img', asset('img/favicons/icon256.png'))" />
    <meta property="og:image:type" content="image/png" />
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
    <script src="{{asset('js/lib/imask.js')}}"></script>

    <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU&coordorder=longlat" type="text/javascript"></script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
    m[i].l=1*new Date();
    for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
    k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(94073132, "init", {
         clickmap:true,
         trackLinks:true,
         accurateTrackBounce:true,
         webvisor:true,
         ecommerce:"dataLayerEc"
    });
    window.dataLayerEc = window.dataLayerEc || [];
 </script>
 <noscript><div><img src="https://mc.yandex.ru/watch/94073132" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
 <!-- /Yandex.Metrika counter -->

    <script src="//code.jivo.ru/widget/Ao9F1DsJ2t" async></script>
    {{-- <script src="//cdn.callibri.ru/callibri.js" type="text/javascript" charset="utf-8"></script> --}}

    <script>
        window.asset = "{{ asset('') }}";
        window.storage = "{{ asset('storage') }}";
    </script>

    @vite([
        'resources/css/app.css',
        'public/fonts/icons/style.css',
        'public/css/main.scss',
        'public/css/tovar_filter.css',
        'public/css/tovar_page_content.css',
        'public/css/catalog_menu.css',
        'public/css/cart.css',
        'public/css/auth.css',
        'public/css/cabinet.css',
        'public/css/mainsearch.css',
        'public/css/pagination.css',


        'resources/js/app.js',
        'public/js/sliders.js',
        'public/js/delivery_zone.js',
        'public/js/filter.js',
        'public/js/map.js',
        'public/js/categories.js',
        'public/js/catalog_menu.js',
        'public/js/cart.js',
        'public/js/favorites.js',
        'public/js/mainsearch.js',
    ])

</head>

<body>
    @include("allicon")
    <x-catalog-menu></x-catalog-menu>
    <x-mobile-bottom-menu></x-mobile-bottom-menu>



	<div class="wrapper" id="global_app">

        <modal-window rout="/send_consult" redirect="/thencs" hesh="floristConsult" title="Помощь флориста" subtitle="Мы свяжемся с Вами в течении 15 минут" showarea="false"></modal-window>
        <modal-window rout="/send_review" redirect="/thencs" hesh="showModalRewiew" title="Оставить отзыв" subtitle="Нам важно Ваше мнение! Ваш отзыв поможет нам стать лучьше!" showarea="true"></modal-window>
        <modal-window rout="/send_consult" redirect="/thencs" hesh="showModalNal" title="Уточнить наличие" subtitle="Укажите ваше имя и телефон. В течение нескольких минут с вами свяжется наш флорист для уточнения наличия" showarea="false"></modal-window>
        <one-click-buy-window></one-click-buy-window>



        @yield('content')

        <section>
            <div class="_wrapper">
                <div class="delivery_comment">
                    <div class="icon">
                        <svg class="sprite_icon">
                            <use xlink:href="#free_delivery_gr"></use>
                        </svg>
                    </div>
                    <div class="text">
                        <p class="free">Бесплатная доставка распространяется на: проспект Победы, Дериглазова, ул. Карла-Маркса</p>
                        <p class="pay">Доставка в другие районы: 150-250 р.</p>
                        <p class="pay">Доставка в ко времени: 500 р.</p>
                    </div>
                </div>
            </div>
        </section>

        <x-footer-map></x-footer-map>
        <x-footer></x-footer>
    </div>
</body>

</html>
