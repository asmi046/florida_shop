# AGENTS.md

Файл с описанием проекта для AI-агентов. Проект: интернет-магазин «Флорида» (florida46.ru) — продажа цветов и подарков.

## Обзор проекта

Laravel 12 (PHP ^8.2) + Vue 3 на фронтенде (сборка через Vite). Админ-панель — Orchid Platform 14. Оплата — ЮKassa (`yoomoney/yookassa-sdk-php`), CRM-интеграция — amoCRM (`amocrm/amocrm-api-library`), складской учёт — МойСклад (собственная интеграция через консольные команды), JSON-LD разметка — `asmi046/laravel-jsonld`, админские «скелеты» — `asmi046/site_skelet`.

Уведомления о заказах отправляются в Telegram через Action-классы (`app/Actions/TelegramSendAction.php` и др.).

## Структура кода

- `app/Http/Controllers/` — контроллеры публичной части: каталог (`TovarController`, `CategoryController`, `SearchController`), корзина/заказ (`CartController`, `PayController`), блог (`BlogController`), отзывы, избранное, кабинет, sitemap, ленты (`FeedController`), тестовый контроллер.
- `app/Models/` — Eloquent-модели (`Product`, `Order`, `OrderItem`, `Cart`, `Category`, `ProductOption` и т.д.), трейты в `app/Models/Concerns/` (например `HasProductOptions`).
- `app/Filters/` — фильтры каталога по query-параметрам: базовый `QueryFilter` (whitelist-стиль: вызывается только метод, имя которого перечислено в списке разрешённых параметров) и `ProductFilter`, `ProductDimensions`.
- `app/Actions/` — одноразовые действия: формирование текста/медиа заказа для Telegram, OneClick и т.п.
- `app/Console/Commands/` — интеграция с МойСклад: `FetchMoySkladData`, `UpdateProductsFromMoySklad`, `UpdatePricesFromMoySkladTechPlans`, `MatchProductsWithMoySklad` и др.
- `app/Orchid/Screens/` — экраны админки Orchid (блог, опции, пользователи, роли, теги продуктов).
- `app/helpers/allHelpers.php` — глобальные хелперы, подключаются через `app/Providers/HelpersLoadProvider.php`.
- `routes/` — `web.php` (основные маршруты), `pages.php` (простые статические страницы через `EasyPageController`), `platform.php` (маршруты Orchid), `api.php` (почти пусто, sanctum), `console.php`, `channels.php`.
- `resources/views/` — Blade-шаблоны (каталог, корзина, кабинет, блог и т.д.).
- `resources/js/` — Vue 3 приложение: `app.js` создаёт Vue-приложение и регистрирует компоненты (корзина, отзывы, модальные окна, OneClickBuy и т.п.), используется Vuex и axios. Некоторые страницы используют «голый» JS из `public/js/` (sliders.js, filter.js, cart.js и др.), также собираемый Vite.
- `public/css/*.scss` и часть JS лежит прямо в `public/` — Vite собирает их как входные точки (см. `vite.config.js`).
- `config/amo.php` — настройки amoCRM, включая ID полей UTM-меток.

## Команды сборки и разработки

- `composer install` — зависимости PHP.
- `npm install` — зависимости JS.
- `npm run dev` — Vite dev-сервер.
- `npm run build` — продакшн-сборка фронтенда (результат — `public/build/`).
- `php artisan serve` / обычный веб-сервер — запуск сайта.
- `php artisan migrate`, `php artisan db:seed` — миграции и сидеры.
- Форматирование PHP — Laravel Pint (`vendor/bin/pint`), установлен как dev-зависимость.

## Деплой

Деплой выполняется на shared-хостинг Beget (`danila1s.beget.tech`, каталог `florida46.ru`) по SSH/SCP через npm-скрипты в `package.json`:

- `npm run deploy_m` — основной сценарий: `vite build` → `send_vite_build` (scp `public/build` на сервер) → `pull_rep` (git pull на сервере) → `optimize_all`.
- Прочие скрипты: `pull_rep`, `migrate_rep`, `seed_rep`, `cache_clear`, `optimize`, `storage_link`, `composer_install/update` и т.д. На сервере используется `php8.4` через `composer.phar`.

## Тестирование

- PHPUnit 11, конфиг — `phpunit.xml`, суites: `tests/Unit` и `tests/Feature`.
- Запуск: `php artisan test` или `vendor/bin/phpunit`.
- Существующие тесты: `PriceRangeTest`, `ProductFilterTest`, `ProductOptionsFilterTest`, `ProductTagAdminTest`, `ProcessingPlanFieldTest` (в основном покрывают фильтры каталога и админку тегов).
- Фабрики — `database/factories/`, сидеры — `database/seeders/` (в т.ч. `ProductOptionSeeder`, который деплоится отдельной командой `seed_rep_one`).

## Соглашения по коду

- Комментарии и документация пишутся на русском языке — при добавлении комментариев придерживайтесь этого.
- Контроллеры тонкие, переиспользуемая логика выносится в `app/Actions/*Action` (классы с методом `handle()`).
- Фильтрация списков — через классы-фильтры на базе `App\Filters\QueryFilter` с явным whitelist разрешённых query-параметров; при добавлении нового параметра фильтра добавляйте его метод и имя в список разрешённых.
- Модели, участвующие в админке Orchid, используют трейты `AsSource` и `Filterable`.
- Внешние интеграции (МойСклад, amoCRM, ЮKassa, Telegram) настраиваются через `.env`; ключи для amoCRM и карта UTM-полей — в `config/amo.php`. При работе с заказами учитывайте UTM-поля в таблице `orders` (миграция `2026_09_05_165000_add_utm_fields_to_orders_table.php`).
- Код фронтенда смешанный: Vue-компоненты в `resources/js/components/` и традиционные JS-модули в `public/js/`; входные точки для Vite перечислены в `vite.config.js` — при добавлении нового JS/CSS файла из `public/` добавьте его туда.

## Безопасность

- Секреты только через `.env` ( amoCRM, ЮKassa, Telegram, БД), в код и конфиги-коммиты не добавлять.
- `QueryFilter` защищает от вызова произвольных методов через query-строку — не убирайте whitelist.
- Скрипты деплоя в `package.json` содержат SSH-доступ к продакшн-серверу; запускать их следует осознанно.

## Прочее

- `promts/` — каталог с промтами, использовавшимися для генерации контента/страниц проекта; `promts.txt` — общий файл промтов.
- `TestController` и маршрут `/test_api` — для отладки интеграций; перед продакшном проверяйте, что отладочное не утекает.
