<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Зона доставки</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU&coordorder=longlat" type="text/javascript"></script>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>

        @vite([
            'resources/css/app.css',
            'resources/js/app.js' ,
            'public/js/delivery_zone.js'
            ])
    </head>

    <body id = "global_app" class="antialiased">
        <h1>Зона доставки</h1>

        <map-in-page></map-in-page>

    </body>
</html>
