# План: ajax-фильтрация каталога без перезагрузки страницы

## Контекст

Проект florida_shop (Laravel 12 + Blade, сборка Vite). Фильтр каталога — GET-форма
`resources/views/components/tovar-filter.blade.php`, JS-логика в `public/js/filter.js`
(там сейчас только мобильная панель и аккордеоны). Фильтрация серверная через
`App\Filters\ProductFilter`, вызывается как `Product::filter($request)`.

Цель: при выборе чекбокса/опции фильтра список товаров и пагинация обновляются
через fetch без полной перезагрузки, URL синхронизируется через history.pushState.

## Затронутые места

- Контроллеры: `app/Http/Controllers/CategoryController.php` (методы `catalog`,
  `show_cat`), аналогично проверить `TovarController`/`SearchController` и другие,
  где используется `ProductFilter` и фильтр (price, tag, search страницы).
- Шаблоны: `resources/views/category.blade.php`, `catalog.blade.php`,
  `price.blade.php`, `tag.blade.php`, `search.blade.php` — список товаров и
  пагинация повторяются везде.
- JS: `public/js/filter.js` (проверить, что входит в entry-points `vite.config.js`).
- Пагинация: компонент `resources/views/components/pagination.blade.php`.

## Шаги реализации

1. **Partial-шаблон.** Создать `resources/views/partials/product-list.blade.php`,
   перенести туда блок списка товаров (обёрнуть в `<div id="product-list">`) и
   пагинацию (`<div id="product-pagination">`). Подключить через
   `@include('partials.product-list')` в category/catalog/price/tag/search.

2. **Контроллеры.** В каждом методе, отдающем список товаров, добавить ветку:
   ```php
   if ($request->ajax()) {
       return response()->json([
           'html' => view('partials.product-list', ['allproduct' => $allproduct])->render(),
           'count' => $allproduct->total(),
       ]);
   }
   ```
   Отдельный роут НЕ создавать — использовать существующие методы.

3. **JS в `public/js/filter.js`**:
   - на `change` по `input[type="checkbox"], select` внутри `.tovar_filter`
     вызывать `applyFilter(form)`;
   - собирать параметры через `new URLSearchParams(new FormData(form))`,
     удалять `page` (сброс на 1-ю страницу), слать fetch с заголовком
     `X-Requested-With: XMLHttpRequest`;
   - подменять `#product-list` и `#product-pagination` из пришедшего HTML
     (парсить через временный `<div>`);
   - `history.pushState` с новым URL, обработка `popstate` (перезагрузка);
   - при `!res.ok` (например, 404 при выходе за страницы) — перезапрос без `page`;
   - лёгкая индикация загрузки (opacity/спиннер на `#product-list`);
   - после подмены скролл к `#product-list`.

4. **Пагинация.** Перехватывать клики по ссылкам в `#product-pagination`,
   грузить ту же страницу через `applyFilter`-логику по href.

5. **Цена «От/До».** Не отправлять авто-submit по `change` на текстовых полях:
   отправка по `blur`/`Enter` (с debounce ~500 мс) или оставить кнопку
   «Применить» только для цены.

6. **Кнопки «Применить»/«Сбросить фильтр».** При авто-submit «Применить» скрыть
   или оставить для цены; «Сбросить» перевести на JS-очистку формы + submit.

## Проверка

- `npm run build` проходит; `php artisan test` зелёный.
- Вручную на странице категории: выбор чекбокса обновляет список без перезагрузки,
  URL меняется, кнопка «назад» браузера работает, пагинация грузится ajax-ом,
  сброс фильтра возвращает полный список, мобильная панель фильтра работает.
- Проверить страницы price/tag/search — фильтр ведёт себя одинаково.
