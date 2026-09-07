Мне нужно собирать следующие utm метки:

utm_source

utm_medium

utm_campaign

utm_term

utm_content

utm_referrer

А далее передавать их в amoCRM. Для сопряжения с amoCRM в проекте есть ервис @app/Services/AmoApiSevice.php

Предложи варианты реализации.

---

Давай реализуем

Шаг 1: Собираем метки через Middleware.
Шаг 2: Вариант 2A. Колонки в orders

Шаг 3: не реализуем я поищу информацию в документации amoCRM и дам конкретные указания

Теперь давай реализуем Шаг 3.

Вот ID полей в amoCRM:

```php
'utm_fields' => [
    'utm_source'   => 657815, // ← заменить на реальные ID
    'utm_medium'   => 657811,
    'utm_campaign' => 657813,
    'utm_term'     => 657817,
    'utm_content'  => 657809,
    'utm_referrer' => 657819,
],
```
