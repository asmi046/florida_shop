<?php

/*
|--------------------------------------------------------------------------
| Палитра цветов товара
|--------------------------------------------------------------------------
|
| Единственный источник истины по оттенкам для параметра «Цвет».
| Ключ — слаг значения параметра (совпадает со слагом в справочнике
| product_options), значение — hex-код кружка или CSS-градиент.
|
| Палитра заливается в базу сидером ProductOptionSeeder в product_options.meta,
| поэтому её можно переиспользовать в YML-фиде и на карточке товара,
| а администратор может переопределить оттенок значения без правки кода.
|
*/

return [
    'bezevyi' => ['title' => 'Бежевый', 'hex' => '#E8DCC8'],
    'belyi' => ['title' => 'Белый', 'hex' => '#FFFFFF'],
    'bordovyi' => ['title' => 'Бордовый', 'hex' => '#7B1E2B'],
    'goluboi' => ['title' => 'Голубой', 'hex' => '#87CEEB'],
    'zeltyi' => ['title' => 'Желтый', 'hex' => '#FFD400'],
    'zelenyi' => ['title' => 'Зеленый', 'hex' => '#2E7D32'],
    'koricnevyi' => ['title' => 'Коричневый', 'hex' => '#8B5A2B'],
    'krasnyi' => ['title' => 'Красный', 'hex' => '#D32F2F'],
    'kremovyi' => ['title' => 'Кремовый', 'hex' => '#FFF3D6'],
    'malinovyi' => ['title' => 'Малиновый', 'hex' => '#C2185B'],
    'miks' => [
        'title' => 'Микс',
        'gradient' => 'linear-gradient(135deg, #D32F2F 0%, #FFD400 25%, #7FD1AE 50%, #1565C0 75%, #9C27B0 100%)',
    ],
    'miatnyi' => ['title' => 'Мятный', 'hex' => '#7FD1AE'],
    'oranzevyi' => ['title' => 'Оранжевый', 'hex' => '#FB8C00'],
    'persikovyi' => ['title' => 'Персиковый', 'hex' => '#FFAB91'],
    'purpurnyi' => ['title' => 'Пурпурный', 'hex' => '#9C27B0'],
    'raznocvetnyi' => [
        'title' => 'Разноцветный',
        'gradient' => 'conic-gradient(#D32F2F, #FFD400, #7FD1AE, #1565C0, #9C27B0, #D32F2F)',
    ],
    'rozovyi' => ['title' => 'Розовый', 'hex' => '#F48FB1'],
    'sinii' => ['title' => 'Синий', 'hex' => '#1565C0'],
    'sirenevyi' => ['title' => 'Сиреневый', 'hex' => '#B39DDB'],
    'fioletovyi' => ['title' => 'Фиолетовый', 'hex' => '#6A1B9A'],
    'cernyi' => ['title' => 'Черный', 'hex' => '#212121'],
];
