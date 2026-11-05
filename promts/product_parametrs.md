Смотри в проекте есть модель app/Models/Product.php

И есть фильтры реализованные в папке app/Filters

Сейчас фильтрация реализована по цене и пометкам новинка хит и т. д.

Есть потребность добавить к модели параметры для фильтрации, такие как:

- Цвет
- Упаковка
- Событие
- Состав
- Кому

У всех этих параметров возможен множественный выбор.

Как лучше организовать добавление этих параметров с возможностью последующей фильтрации?
Предлажи варианты архитектуры.

# Параметры товара с множественным выбором и фильтрация

Документ описывает варианты архитектуры для добавления к модели `Product` фильтруемых
параметров: **Цвет, Упаковка, Событие, Состав, Кому** (все с множественным выбором)
и даёт рекомендацию по реализации.

---

## 1. Что уже есть в проекте (точка отсчёта)

| Слой        | Файл                                                | Что сделано                                                                                                                                                        |
| ----------- | --------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Модель      | `app/Models/Product.php`                            | `tovar_categories` (belongsToMany), `tovar_celebration` (belongsToMany), `tags` (belongsToMany `ProductTag`), `product_images` (hasMany), `consist` (JSON-колонка) |
| Фильтр      | `app/Filters/QueryFilter.php`                       | Перебирает `$request->query()`, для каждого параметра ищет метод с таким же именем и вызывает его                                                                  |
| Фильтр      | `app/Filters/ProductFilter.php`                     | `minprice`, `maxprice`, `order`, `brand`, `subcat`, `osobennosti`                                                                                                  |
| Scope       | `Product::scopeFilter()`                            | `Product::filter($filter)`                                                                                                                                         |
| Фронт       | `resources/views/components/tovar-filter.blade.php` | Аккордеон-блоки, чекбоксы, `value_check()` helper                                                                                                                  |
| Фронт       | `app/View/Components/ProductTagInFilter.php`        | Пример class-based компонента с `Cache::remember`                                                                                                                  |
| Админка     | `app/Orchid/Screens/Product/ProductEditScreen.php`  | `Relation::make('tags.')->multiple()` + `sync()` в `save_info` — готовый паттерн мультивыбора                                                                      |
| Справочники | `Category`, `Celebration`, `ProductTag`             | Каждый — своя таблица + pivot + Orchid-экран                                                                                                                       |

**Важно:** из пяти запрошенных параметров **один уже реализован** — `Celebration`
(`app/Models/Celebration.php`, таблица `celebrations` + pivot `celebration_product`).
Это и есть «Событие» (8 марта, День рождения и т.д.). Он уже имеет SEO-страницы, sitemap,
роут и админку. **Решено (раздел 7):** `Celebration` остаётся как есть и в фильтре
не участвует, а для «События» в новом справочнике заводятся свои записи с типом `event`.

**Второй важный момент:** «Состав» в проекте уже есть, но в другой форме — это JSON-колонка
`products.consist` вида `[{Имя: 'Роза', Количество: 5}, ...]`
(`database/migrations/2023_12_20_132532_add_consist_products_table copy.php`).
Это не словарь значений, а произвольный список, введённый руками.
**Решено (раздел 7):** JSON остаётся как есть и продолжает питать карточку и YML-фид,
а фильтр «Состав» работает по отдельным записям справочника с типом `composition`.

**Третье:** `ProductTag` выводится в фильтре под заголовком **«Цветы»** и ведёт на SEO-страницы
(`/tags/{slug}`). Это рубрикатор, а не «Цвет» (красный/розовый/белый). Не путать.

---

## 2. Что сломано в текущей реализации — уже исправлено

Это были не «блаж», а реальные 500-ки на проде. Исправлено отдельным набором изменений
до начала работы над новыми параметрами — заодно это даёт **расширяемую точку входа**
для мультивыбора из раздела 5.

| # | Проблема | Где | Как исправлено |
|---|---|---|---|
| 1 | `?apply=1` → 500 (вызов служебного метода фильтра через query-строку) | `app/Filters/QueryFilter.php` | Вместо `method_exists()` — **whitelist** `$allowed` + запрет служебных методов `$reserved`. Любой параметр вне списка игнорируется |
| 2 | `?osobennosti[]=` → 500 (`ArgumentCountError`: метод вызывался без аргументов) | `app/Filters/QueryFilter.php` | Пустые значения пропускаются (`$arguments === [] → continue`), у всех методов фильтра добавлены значения по умолчанию |
| 3 | `osobennosti` работал как AND, а не OR | `app/Filters/ProductFilter.php` | Группа условий обёрнута в `where(function ($q) { ... orWhere ... })` → «Новинки **или** Хит **или** Скидка» |
| 4 | `?brand=x` / `?subcat[]=1` → 500 (нет таких колонок) | `app/Filters/ProductFilter.php` | Мёртвые методы `brand`/`subcat` удалены, убрано скрытое поле `brand` из `tovar-filter.blade.php` |
| 5 | Фильтр не применялся на странице категории | `app/Http/Controllers/CategoryController.php` | `show_cat()` принимает `ProductFilter` и вызывает `->filter($filter)` |
| 6 | `?order=` (пустое) отключало сортировку по умолчанию | `app/Http/Controllers/PriceController.php` | Вместо `array_key_exists` — `$filter->has('order')` (новый метод в `QueryFilter`) |

Регрессии закрыты тестами `tests/Feature/ProductFilterTest.php` (7 тестов, 30 assertions):
неизвестные и пустые параметры не роняют страницу, `osobennosti` работает по «ИЛИ»,
фильтр применяется на странице категории, сортировка по умолчанию сохраняется.

> **Что осталось на будущее:** `order` по-прежнему сравнивается с русскими строками
> (`'Сначала дешевые'`). При унификации фильтров стоит уйти на slug
> (`price_asc`, `price_desc`, `title`) — это отдельная задача, не блокирует новые параметры.

### 2.1 Почему whitelist — это и есть точка входа для новых параметров

Новый мультивыбор из раздела 5 добавляется **одной строкой** в `$allowed` у `ProductFilter`
плюс методом-фильтром. Никакой магии по именам методов, никаких рисков вызова
служебных методов, никакой правки самого `QueryFilter` при каждом новом параметре.

Актуальный вид `QueryFilter::apply()` после правки:

```php
public function apply(Builder $builder)
{
    $this->builder = $builder;

    foreach ($this->filters() as $name => $value) {
        if (! $this->isAllowed($name)) {
            continue;
        }

        $arguments = array_filter([$value]);

        if ($arguments === []) {
            continue;
        }

        call_user_func_array([$this, $name], $arguments);
    }

    return $this->builder;
}
```

---

## 3. Требования к новым параметрам

- Множественный выбор **внутри** одного параметра → «ИЛИ» (товар с любым из выбранных значений).
- Разные параметры → «И» (товар должен удовлетворять всем выбранным параметрам).
- Параметры должны:
    - редактироваться в админке (Orchid) — и типы, и значения, и привязка к товару;
    - отображаться на карточке товара и на странице товара;
    - участвовать в фильтре на `/catalog`, `/catalog/{category}`, `/prices/{slug}`, `/tags/{slug}`;
    - желательно — показывать счётчики («Красный (12)») и скрывать пустые значения;
    - желательно — уметь приехать из МойСклад (в проекте уже есть интеграция).

---

## 4. Варианты архитектуры

### Вариант A. Отдельный справочник на каждый параметр (по образцу `Category`/`Celebration`)

```
colors / packagings / audiences / compositions        — таблицы-справочники
product_color / product_packaging / ...               — pivot-таблицы
```

В `Product`: `colors()`, `packagings()`, `audiences()` — всё `belongsToMany`.
В Orchid — `Relation::make('colors.')->multiple()` (работает из коробки).
Фильтр — по одному методу на параметр.

**Плюсы:** максимальная простота и читаемость; FK-целостность; быстрые индексные джойны;
бесплатная поддержка Orchid; предсказуемые планы запросов.
**Минусы:** 6–8 новых таблиц; добавление нового типа параметра = миграция + модель +
relation + метод фильтра + правка админки; дублирование кода; «Состав» с количествами
в эти таблицы не ложится без отдельной обработки.

**Когда выбирать:** если набор параметров фиксирован навсегда и их ровно 4–5.

---

### Вариант B. EAV: `attributes` + `attribute_values` + единый pivot

```
product_attributes      (id, slug, title, position, show_in_filter)
attribute_values        (id, attribute_id, title, slug, position, meta json)
product_attribute_value (product_id, attribute_value_id)
```

**Плюсы:** новый тип параметра = одна строка в `product_attributes` + значения, без миграций
и без кода; фильтр и UI полностью генерируемые; счётчики считаются одной группировкой;
«Цвет» естественно хранит `meta.hex`.
**Минусы:** EAV в MySQL — «антипаттерн», неудобно делать отчёты и сложные выборки;
`whereHas` по EAV даёт больше джойнов; Orchid не умеет из коробки relation на вложенный
pivot — нужен кастомный `Select` и ручной `sync()`; сложнее отладка;
целостность данных не гарантируется на уровне БД.

**Когда выбирать:** если параметров будет много и они будут добавляться часто
(маркетплейс-подобный сценарий, где набор атрибутов регулярно расширяется).

---

### Вариант C. JSON-колонка на `products` + справочник значений

`products.attributes` → `{"color":[3,7],"packing":[1]}`. Фильтр через
`JSON_OVERLAPS(attributes->'$.color', ...)`.

**Плюсы:** ноль джойнов, читается прямо из строки товара; новый параметр = ноль миграций;
прецедент в проекте есть (`consist`).
**Минусы:** требует MySQL 8.0.17+ (`JSON_OVERLAPS`) — надо проверить версию на проде;
индексация мультизначений в JSON больна (нужны generated-колонки на каждое значение —
то есть фактически снова отдельные колонки); счётчики и «какие товары имеют этот параметр»
считаются медленнее; FK-целостности нет; удаление значения из справочника оставляет «сирот»;
Orchid-UI для выбора значений придётся писать руками.

**Когда выбирать:** если справочник значений маленький и стабильный, а объём товаров
невелик (уровень текущего каталога). Хрупко по MySQL-версии.

---

### Вариант D. Единый справочник значений + единый pivot (рекомендуется)

Справочник **типов** + справочник **значений** + **одна** таблица связей.
Все пять параметров (включая «Событие» и «Состав») живут в этом справочнике.

```
product_option_types               — типы параметров
  id, slug, title, position, show_in_filter, timestamps
  значения: color, packing, event, composition, audience

product_options                    — значения параметров
  id, product_option_type_id, title, slug, position, external_id, meta JSON NULL, is_active, timestamps
  meta: {"hex":"#e53935"} для цвета, {"image":"..."} , {"price_delta": 500} для упаковки

product_product_option             — связь товар ↔ значение
  id, product_id, product_option_id, quantity INT NULL, sort INT NULL
  UNIQUE(product_id, product_option_id)
```

**Плюсы:**

- 3 новые таблицы вместо 8; новая таблица на существующие справочники не влияет.
- Новый тип параметра = INSERT строки в `product_option_types` + значения, **без миграций и кода**.
- Один механизм покрывает все 5 параметров — фильтр, фронт и админка идут одним кодовым путём.
- Ничего не мигрируется: `celebrations` и `products.consist` остаются на своих местах
  и не участвуют в фильтре (решение в разделе 7).
- `meta JSON` покрывает специфику: hex для цвета, картинка/наценка для упаковки.
- Счётчики (фаза 2) — одна группировка по pivot, без спецкейсов.
- `external_id` заложен сразу — будущая синхронизация из МойСклад подключается без миграции схемы.

**Минусы:**

- Relation в Orchid для `productOptions` не группируется по типам «из коробки»
  (лечится лейблом `Цвет · Красный` в заголовке или кастомным `Select`).
- Состав вводится дважды: JSON `consist` (для отображения) и записи справочника
  (для фильтра) — лечится командой-синхронизатором.

---

## 5. Рекомендация

**Вариант D** — единый справочник значений с одним pivot. Все пять параметров
в справочнике, существующие `Celebration` и `consist` не трогаем.

Причины именно такого выбора:

1. Набор из 5 параметров задан и не будет «расти по 2 в месяц» → не нужен полноценный EAV,
   но нужна возможность докинуть 6-й параметр без миграции.
2. Ничего не мигрируется → нет риска сломать SEO-страницы праздников и YML-фид
   для Яндекс.Маркета.
3. `consist` остаётся источником для отображения, а фильтр по составу работает
   по отдельным записям справочника — состав без количеств в фильтре не нужен.
4. Проект уже использует `belongsToMany` + `sync()` + `Relation::make()->multiple()`
   (теги, категории, праздники) — новое решение будет выглядеть «как родное», а не как
   отдельный фреймворк внутри проекта.

### 5.1 Схема БД

```php
// product_option_types
Schema::create('product_option_types', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();       // color, packing, event, composition, audience
    $table->string('title');                 // Цвет, Упаковка, Событие, Состав, Кому
    $table->unsignedSmallInteger('position')->default(0);
    $table->boolean('show_in_filter')->default(true);
    $table->timestamps();
});

// product_options
Schema::create('product_options', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_option_type_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->string('slug');
    $table->string('image')->nullable();     // опционально, для упаковки/состава
    $table->string('external_id')->nullable(); // ключ из МойСклад, для будущей синхронизации
    $table->json('meta')->nullable();        // {"hex":"#e53935"} / {"price_delta":500}
    $table->unsignedSmallInteger('position')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->unique(['product_option_type_id', 'slug']);
    $table->index(['product_option_type_id', 'position']);
    $table->index(['external_id']);
});

// product_product_option
Schema::create('product_product_option', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_option_id')->constrained()->cascadeOnDelete();
    $table->unsignedInteger('quantity')->nullable();   // для «Состав»
    $table->unsignedSmallInteger('sort')->default(0);
    $table->timestamps();

    $table->unique(['product_id', 'product_option_id']);
    $table->index(['product_option_id', 'product_id']);  // под фильтр по значению
});
```

### 5.2 Модели

```php
// app/Models/ProductOptionType.php
class ProductOptionType extends Model
{
    protected $fillable = ['slug', 'title', 'position', 'show_in_filter'];
    protected $casts = ['show_in_filter' => 'boolean'];

    public function options() { return $this->hasMany(ProductOption::class)->orderBy('position'); }
    public function products() { return $this->belongsToMany(Product::class, 'product_product_option'); }
}

// app/Models/ProductOption.php
class ProductOption extends Model
{
    protected $fillable = ['product_option_type_id', 'title', 'slug', 'image', 'external_id', 'meta', 'position', 'is_active'];
    protected $casts = ['meta' => 'array', 'is_active' => 'boolean'];

    public function type() { return $this->belongsTo(ProductOptionType::class, 'product_option_type_id'); }
    public function products() { return $this->belongsToMany(Product::class, 'product_product_option')
        ->withPivot(['quantity', 'sort']); }
}
```

В `Product` — трейт с relation и хелперами:

```php
// app/Models/Concerns/HasProductOptions.php
trait HasProductOptions
{
    public function productOptions()
    {
        return $this->belongsToMany(ProductOption::class, 'product_product_option',
            'product_id', 'product_option_id')->withPivot(['quantity', 'sort']);
    }

    // Быстрый доступ к значениям конкретного типа: $product->optionsOfType('color')
    public function optionsOfType(string $typeSlug)
    {
        return $this->productOptions()
            ->whereHas('type', fn ($q) => $q->where('slug', $typeSlug));
    }
}
```

`Product` использует `HasProductOptions` **в дополнение** к существующим
`tovar_categories` / `tovar_celebration` / `tags`.

> **Не добавлять `productOptions` в `$with` модели.** В `Product::$with` уже висят
> `tovar_categories` и `product_images`; третий relation с pivot-джойном на каждом
> запросе каталога (16 товаров на страницу) — лишняя работа. Загружать адресно:
> `Product::with('productOptions.type')` на странице товара, отдельный scope
> `withProductOptions()` — на карточках, где реально нужно показать цвет/упаковку.

### 5.3 Дескриптор измерений — единая точка правды

**Решение принято:** `Celebration` остаётся как есть (таблица `celebrations`, pivot
`celebration_product`, SEO-страницы, sitemap, Orchid-relation) — он **не участвует
в фильтре**. В справочнике параметров для «События» будут **собственные записи**
с типом `event`. Аналогично: JSON-поле `products.consist` остаётся как есть и продолжает
использоваться для вывода состава на карточке и в YML-фиде, а «Состав» в фильтре —
это записи справочника с типом `composition`.

Благодаря этому все пять измерений работают **через один механизм** — одну таблицу
значений и один pivot. Двух разных форм («своя таблица» / «справочник») больше нет,
значит фильтр, фронт и админка получают один кодовый путь.

```php
// app/Filters/ProductDimensions.php
class ProductDimensions
{
    public static function all(): array
    {
        return [
            'color' => [
                'title' => 'Цвет',
                'type'  => 'color',
            ],
            'packing' => [
                'title' => 'Упаковка',
                'type'  => 'packing',
            ],
            'event' => [
                'title' => 'Событие',
                'type'  => 'event',
            ],
            'composition' => [
                'title' => 'Состав',
                'type'  => 'composition',
            ],
            'audience' => [
                'title' => 'Кому',
                'type'  => 'audience',
            ],
        ];
    }
}
```

Один дескриптор покрывает все измерения: значение каждого лежит в `product_options`,
тип определяется через `product_option_type_id`. Фильтр, фронт и админка читают этот один
массив — добавить измерение = одна строка в массиве + запись в БД.

### 5.4 Фильтр

**Whitelist в `QueryFilter` уже готов (раздел 2).** Текущий вид:

```php
// app/Filters/QueryFilter.php
class QueryFilter
{
    public $request;
    protected $builder;
    protected $delimiter = ',';

    /** Разрешённые query-параметры. Метод с таким же именем будет вызван. */
    protected array $allowed = [];

    /** Служебные методы, вызов которых через query-строку запрещён. */
    protected array $reserved = ['apply', 'filters', 'paramToArray'];

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (! $this->isAllowed($name)) {
                continue;
            }

            $arguments = array_filter([$value]);

            if ($arguments === []) {
                continue;
            }

            call_user_func_array([$this, $name], $arguments);
        }

        return $this->builder;
    }
}
```

**Далее — общий мультивыбор в `ProductFilter`:**

```php
// app/Filters/ProductFilter.php
class ProductFilter extends QueryFilter
{
    protected array $allowed = [
        'minprice', 'maxprice', 'order', 'osobennosti',
        'color', 'packing', 'event', 'composition', 'audience',
    ];

    /**
     * Мультивыбор по параметрам: внутри измерения — ИЛИ, между измерениями — И.
     * Значения приходят как ?color[]=1&color[]=2
     */
    public function applyDimensions(Builder $builder): Builder
    {
        foreach (ProductDimensions::all() as $slug => $dimension) {
            $ids = $this->dimensionIds($slug);

            if ($ids === []) {
                continue;
            }

            $typeId = ProductOptionType::where('slug', $dimension['type'])->value('id');

            if ($typeId === null) {
                continue;
            }

            $builder->whereHas('productOptions', function ($query) use ($ids, $typeId) {
                $query->where('product_options.product_option_type_id', $typeId)
                    ->whereIn('product_options.id', $ids);
            });
        }

        return $builder;
    }

    protected function dimensionIds(string $slug): array
    {
        $raw = $this->request->query($slug);

        if ($raw === null || $raw === '') {
            return [];
        }

        return collect((array) $raw)
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
    }
}
```

**Куда вызвать `applyDimensions`?** `QueryFilter::apply()` перебирает только whitelist-параметры
и по имени метода. `applyDimensions` — метод с другим именем, в whitelist его нет, значит
он не вызовется автоматически. Два чистых варианта:

- **Вариант 1 (рекомендую).** Добавить в `QueryFilter::apply()` вызов хука:

    ```php
    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        // ... перебор разрешённых методов
        $this->builder = $this->applyExtra($this->builder);   // хук
        return $this->builder;
    }
    protected function applyExtra(Builder $builder): Builder { return $builder; }
    ```

    В `ProductFilter`:

    ```php
    protected function applyExtra(Builder $builder): Builder
    {
        return $this->applyDimensions($builder);
    }
    ```

    Тогда **все** контроллеры, вызывающие `->filter($filter)`, автоматически получают
    мультивыбор — включая те, которые появятся позже.

- **Вариант 2.** Вызывать `applyDimensions()` явно в каждом контроллере. Явнее, но
  легко забыть в новом контроллере → фильтр «молча» не работает.

**Семантика запроса.** `whereHas` + `whereIn` даёт:

- внутри одного измерения — ИЛИ (товар с любым из выбранных значений);
- между измерениями — И (отдельный `whereHas` на каждое);
- дубликатов строк нет (`whereHas` → `EXISTS`-подзапрос, а не `JOIN`).

Эквивалентная «быстрая» форма, если понадобится:

```php
$builder->whereIn('id', fn ($q) => $q->select('product_id')
    ->from('product_product_option')
    ->join('product_options', 'product_options.id', '=', 'product_product_option.product_option_id')
    ->whereIn('product_options.id', $ids)
    ->where('product_options.product_option_type_id', $typeId));
```

**Починить `osobennosti` (AND → OR):**

```php
public function osobennosti($osobennosti)
{
    $this->builder->where(function ($q) use ($osobennosti) {
        foreach ((array) $osobennosti as $osb) {
            match ($osb) {
                'Скидка'     => $q->orWhere('old_price', '>', 0),
                'Хит продаж' => $q->orWhere('hit', 1),
                'Новинки'    => $q->orWhere('new', 1),
                default      => null,
            };
        }
    });
}
```

Убрать `brand` и `subcat` (мёртвые, дают 500).

### 5.5 Фронт

Новый компонент по образцу `ProductTagInFilter`:

```php
// app/View/Components/ProductOptionsFilter.php
class ProductOptionsFilter extends Component
{
    public function __construct(public string $dimension) {}

    public function render()
    {
        $values = Cache::remember("product_options_{$this->dimension}", 3600, fn () =>
            ProductOption::where('product_option_type_id', $typeId)
                ->where('is_active', true)
                ->orderBy('position')->orderBy('title')->get()
        );

        return view('components.product-options-filter', compact('values'));
    }
}
```

Разметка — в стиле существующего аккордеона (`acc_blk` / `acc_head` / `acc_body` / `ch_label`):

```blade
<div class="acc_blk">
    <div class="acc_head open">
        <span>{{ $title }}</span>
    </div>
    <div class="acc_body open">
        @foreach ($values as $value)
            <label class="ch_label">
                <input type="checkbox" name="{{ $dimension }}[]" value="{{ $value->id }}"
                    {{ in_array($value->id, (array) request()->query($dimension, [])) ? 'checked' : '' }}>
                @if ($dimension === 'color' && ($value->meta['hex'] ?? null))
                    <i class="filter_swatch" style="background: {{ $value->meta['hex'] }}"></i>
                @endif
                <span>{{ $value->title }}</span>
            </label>
        @endforeach
    </div>
</div>
```

Подключение в `tovar-filter.blade.php` — в цикле по дескриптору, чтобы фронт тоже не
зависел от количества параметров:

```blade
@foreach (\App\Filters\ProductDimensions::all() as $slug => $dimension)
    <x-product-options-filter :dimension="$slug" :title="$dimension['title']" />
@endforeach
```

> **URL-параметры: id или slug?** Рекомендую **id** (`?color[]=12`) — проще, нет доп.
> запроса на маппинг slug→id, устойчиво к переименованию значений. Если важна
> «красота» URL и шэринга ссылок — использовать slug и кэшировать маппинг
> `slug → id` (Redis/`Cache::remember`), тогда чекбоксы будут `value="{{ $value->slug }}"`.

### 5.6 Админка (Orchid)

1. **Справочники:** новые экраны `ProductOptionTypeListScreen` и `ProductOptionListScreen`
   (CRUD + `Attach`/`Detach` — по образу `ProductTagListScreen`).
2. **Карточка товара** (`ProductEditScreen`) — relation на новые параметры:
    ```php
    Relation::make('productOptions.')
        ->fromModel(ProductOption::class, 'title', 'id')
        ->title('Параметры товара')
        ->value($this->product->productOptions)
        ->multiple()
        ->help('Выберите значения параметров'),
    ```
    и в `save_info()`:
    ```php
    $this->product->productOptions()->sync($request->get('productOptions'));
    ```
    Если нужен разнос по типам — вместо одного поля сделать динамический набор `Select`
    (по одному на тип) и синхронизировать вручную по `type_id`.
3. **Количество для «Состава»**: Orchid `Relation` не умеет писать в pivot `quantity`.
   Для состава нужен кастомный field (или `Matrix`-подход, как сейчас у `consist`).

### 5.7 Миграция существующих данных

**Решение принято: ничего не мигрируем.** Существующие данные остаются на своих местах,
новый справочник наполняется заново.

| Источник                                | Что делаем                                                                                                                                          | Сложность |
| --------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- | --------- |
| `celebrations` + `celebration_product`   | Остаётся как есть: SEO-страницы праздников, sitemap, Orchid-relation. В фильтре **не участвует** — для «События» в справочнике свои записи с типом `event` | нулевая   |
| `products.consist` (JSON)                | Остаётся как есть: продолжает питать вывод состава на карточке и YML-фид. Фильтр «Состав» работает по записям справочника с типом `composition` | нулевая   |
| `product_tags`                           | Остаётся как есть (SEO-рубрикатор «Цветы»), в новую схему не переносится                                                                             | нулевая   |
| Новые значения (цвет, упаковка, событие, состав, кому) | Заполняются в админке вручную либо сидом                                                                            | —         |

**Следствие: рискованных миграций в проекте нет.** В частности, не нужно трогать
`resources/views/feed/yml_cactegory.blade.php` (выгрузка в Яндекс.Маркет) и
`resources/views/components/tovar-page-content/tovar-info.blade.php` — они продолжают
работать со старым JSON.

> **Нюанс двойного ввода.** Состав теперь вводится дважды: JSON-поле `consist`
> (для отображения с количествами) и записи справочника типа `composition`
> (для фильтра). Чтобы снять ручной дубль, предлагается отдельная artisan-команда
> `moysklad:…`-стиля `products:sync-composition`, которая разбирает `consist` и
> проставляет pivot-ссылки (нормализуя `Роза`/`роза`/`Розы` → `Роза`).
> JSON при этом не меняется — команда только дописывает связи. Это опционально,
> но снимает большую часть ручной работы.

### 5.8 Счётчики — фаза 2 (решение принято)

Счётчики («Красный (12)») делаются **отдельным этапом после** рабочего фильтра.
Схема, которую нужно будет реализовать: для каждого измерения считать количество
**в контексте остальных фильтров** (кроме самого этого измерения). Одна группировка
на измерение, не N запросов на значение:

```php
// для измерения X, где X-фильтр снят, а остальные применены:
DB::table('product_product_option')
    ->join('product_options', 'product_options.id', '=', 'product_product_option.product_option_id')
    ->where('product_options.product_option_type_id', $typeId)
    ->whereIn('product_product_option.product_id', $subquery /* базовый запрос без X */)
    ->groupBy('product_product_option.product_option_id')
    ->selectRaw('product_product_option.product_option_id, count(*) as cnt')
    ->pluck('cnt', 'product_option_id');
```

Значения с `cnt = 0` скрываем, но показываем выбранные (чтобы пользователь мог снять галочку).

### 5.9 Производительность

- Индексы: `product_product_option(product_option_id, product_id)` — основной рабочий
  индекс фильтра; `product_product_option(product_id)` — для `sync()` и загрузки relation.
- Справочник значений кэшировать (`Cache::remember`, как уже сделано в `ProductTagInFilter`).
- `whereHas` даёт `EXISTS`-подзапрос → дубликатов нет, `DISTINCT` не нужен.
- На 5 измерений — 5 подзапросов; при текущем объёме каталога это дешевле, чем любой
  EAV/JSON-вариант.

### 5.10 Справочники: значения и слаги

Значения утверждены владельцем проекта. Слаги — результат `Str::slug()` (транслитерация
кириллицы по правилам Laravel), дубликатов нет. Порядок в справочнике = порядок
в фильтре (поле `position`).

**Типы параметров** (таблица `product_option_types`):

| slug | title | position |
| ---- | ----- | -------- |
| `composition` | Состав | 1 |
| `packing` | Упаковка | 2 |
| `color` | Цвет | 3 |
| `audience` | Кому | 4 |
| `event` | Событие | 5 |

**Значения** (таблица `product_options`, `position` = порядок в списке):

Цвет — 21:

```
Бежевый (bezevyi), Белый (belyi), Бордовый (bordovyi), Голубой (goluboi),
Желтый (zeltyi), Зеленый (zelenyi), Коричневый (koricnevyi), Красный (krasnyi),
Кремовый (kremovyi), Малиновый (malinovyi), Микс (miks), Мятный (miatnyi),
Оранжевый (oranzevyi), Персиковый (persikovyi), Пурпурный (purpurnyi),
Разноцветный (raznocvetnyi), Розовый (rozovyi), Синий (sinii),
Сиреневый (sirenevyi), Фиолетовый (fioletovyi), Черный (cernyi)
```

Упаковка — 3: `Сумка (sumka)`, `Коробка (korobka)`, `Бумага (bumaga)`

Кому — 8: `Бабушке (babuske)`, `Воспитателю (vospitateliu)`, `Коллеге (kollege)`,
`Мужчине (muzcine)`, `Подруге (podruge)`, `Ребенку (rebenku)`,
`Руководителю (rukovoditeliu)`, `Сестре (sestre)`

Событие — 26:

```
1 сентября (1-sentiabria), 8 марта (8-marta), 9 мая (9-maia),
14 февраля (14-fevralia), 23 февраля (23-fevralia),
Выписка из роддома (vypiska-iz-roddoma), Выпускной (vypusknoi),
Годовщина (godovshhina), Годовщина свадьбы (godovshhina-svadby),
День рождения (den-rozdeniia), День матери (den-materi), День отца (den-otca),
День семьи (den-semi), День учителя (den-ucitelia), Новоселье (novosele),
Новый год и Рождество (novyi-god-i-rozdestvo), Пасха (pasxa),
Повышение (povysenie), Последний звонок (poslednii-zvonok),
Рождение ребёнка (rozdenie-rebenka), Свидание (svidanie),
Татьянин день (tatianin-den), Торжество (torzestvo),
Траурная флористика (traurnaia-floristika), Хэллоуин (xellouin), Юбилей (iubilei)
```

Состав — 12:

```
Альстромерии (alstromerii), Гвоздики (Диантусы) (gvozdiki-diantusy),
Герберы (gerbery), Гипсофила (gipsofila), Гортензии (gortenzii),
Кустовые розы (kustovye-rozy), Лилии (lilii), Маттиола (mattiola),
Одноголовые розы (odnogolovye-rozy), Розы (rozy),
Ромашки (Танацетум) (romaski-tanacetum), Хризантемы (xrizantemy)
```

Нормализация при внесении: `День Рождения` → `День рождения` (как остальные),
упаковка с прописной (`сумка` → `Сумка`). Названия в скобках у состава
(`Гвоздики (Диантусы)`) сохраняются как есть — они помогают идентифицировать цветок.

---

## 6. План внедрения — выполнен

1. ~~**Починить текущий фильтр**~~ **— сделано** (раздел 2): whitelist в `QueryFilter`,
   `osobennosti` → OR, удалены `brand`/`subcat`, включён `->filter($request)` в
   `CategoryController::show_cat()`, `PriceController` → `$filter->has('order')`.
   Покрыто тестами `tests/Feature/ProductFilterTest.php`.
2. ~~**Миграции + модели**~~ **— сделано**: `product_option_types`, `product_options`,
   `product_product_option`; модели `ProductOptionType`, `ProductOption`,
   трейт `HasProductOptions` в `Product`.
3. ~~**Сид типов**~~ **— сделано**: 5 типов, 72 значения
   (`database/seeders/ProductOptionSeeder.php`, идемпотентный — `updateOrCreate`).
4. ~~**`ProductDimensions`**~~ **— сделано** (`app/Filters/ProductDimensions.php`).
5. ~~**Фильтр**~~ **— сделано**: `applyDimensions` в `ProductFilter` + хук `applyExtra`
   в `QueryFilter` (мультивыбор работает во всех контроллерах с `->filter()`).
6. ~~**Фронт**~~ **— сделано**: компонент `ProductOptionsFilter` + подключение
   в `tovar-filter.blade.php`.
7. ~~**Админка**~~ **— сделано**: экраны справочника (`/admin/product-options`),
   5 relation-полей в `ProductEditScreen` + `sync()`.
8. ~~**Тесты**~~ **— сделано** (`tests/Feature/ProductOptionsFilterTest.php`, 11 тестов):
   мультивыбор ИЛИ/И, фильтр на `/catalog`, `/catalog/{slug}`, `/prices/{slug}`,
   `/tags/{slug}`, отметка выбранных значений, сохранение и снятие значений в админке,
   экран справочника.
9. **Осталось на будущее:** счётчики (фаза 2), синхронизация справочников из МойСклад,
   отображение параметров на карточке товара, мобильная вёрстка фильтра.

---

## 7. Решения по ключевым вопросам

| Вопрос | Решение | Следствие |
| ------ | ------- | --------- |
| **Событие**: переносить ли `Celebration` в справочник? | **Нет.** `Celebration` остаётся как есть (SEO-страницы, sitemap, Orchid). В справочнике для «События» — свои записи с типом `event` | Фильтр работает через один механизм; `celebration_product` не трогаем |
| **Событие**: связывать ли значения фильтра с праздниками? | **Нет сопоставления.** Менеджеры вручную проставляют все параметры на боевом сайте | Два справочника независимы; двойной ввод — осознанная цена |
| **Состав**: мигрировать ли JSON `consist`? | **Нет.** JSON остаётся как есть и продолжает питать карточку и YML-фид. В справочнике — свои записи с типом `composition` | Нет рискованных миграций. Данных для переноса всё равно нет — `consist` пуст у всех 13 товаров (раздел 9.1) |
| **Состав**: состава не хватало? | **Дописать** — добавлены «Розы Сантини» и «Тюльпаны» (итого 14 значений) | Закрыт пробел с категорией «Тюльпаны» (2 товара) и заголовками «Розы Сантини» (4 товара) |
| **Счётчики** | **Фаза 2** | Сначала рабочий фильтр без счётчиков |
| **Цвет**: свотч? | **Да** — кружок с оттенком рядом с названием. Оттенок хранится в `product_options.meta.hex` (для «Микс» и «Разноцветный» — `meta.gradient`), палитра — в `config/product_colors.php` | Значения утверждены владельцем (21 шт.) |
| **Подписи полей в карточке товара** | **Все параметры помечены «для фильтров»**: «Состав для фильтров», «Упаковка для фильтров», «Цвет для фильтров», «Кому для фильтров», «Событие для фильтров». Матрица букета — «Состав букета для страницы» | В админке два разных источника состава, подписи различают их. Клиентские блоки фильтра остаются короткими («Цвет», «Состав») |
| **Упаковка**: наценка? | **По умолчанию — только название** (3 шт.: Сумка, Коробка, Бумага) | `meta.price_delta` остаётся пустым, наценка не выводится. Пересмотреть, если упаковка влияет на цену |
| **Кому**: значения редактируемые? | **Да** (8 шт.) | Стандартный справочник |
| **URL-параметры** | **id** (`?color[]=12`) | Нет доп. запроса на маппинг slug→id |
| **Значения из МойСклад** | **Пока не синхронизируем.** Но в `product_options` закладываем `external_id` (nullable) + индекс | Синхронизация потом подключается без миграции схемы |

---

## 8. Что осталось открытым

1. **Мобильная вёрстка** — в фильтре 5 новых блоков чекбоксов (до 72 значений);
   на мобильном экране аккордеон уже умеет сворачиваться (`filter_mobile_panel` +
   `public/js/filter.js`), но, возможно, нужно ограничить высоту/скролл в панели фильтров.
2. **Отображение на карточке товара** — какие параметры показывать в карточке
   (обычно цвет и «Кому»), нужен рейтинг приоритета. Сейчас параметры видны
   только в фильтре и в админке.
3. **Счётчики** (фаза 2) — «Красный (12)» рядом с чекбоксом.
4. **Наценка за упаковку** — если упаковка влияет на цену, нужно завести
   `meta.price_delta` и выводить её в карточке.
5. **Синхронизация справочников из МойСклад** — поле `external_id` уже есть,
   осталось написать команду.
6. **Массовое заполнение на проде** — основной путь (карточка товара) рассчитан
   на поштучную работу. Для боевой базы шире локальной может понадобиться отдельный
   экран массового присвоения параметра (значение → список товаров).

---

## 8.1 Попутно найденные и исправленные баги

| Баг | Где | Как исправлено |
|---|---|---|
| `Undefined array key "processingPlanId"` при сохранении товара без этого поля | `ProductEditScreen::save_info()` | `$new_data['processingPlanId'] ?: null` → `?? null`. Раньше — Warning и обнуление поля техкарты |
| Утечка 2 output-буферов на каждом рендере страницы тега + пустые `<title>` и `<meta description>` | `resources/views/tag.blade.php` | `$tag_info['seo_title']` без `??` давало `null`, а `@section('title', null)` в Blade открывает буфер, который не закрывается. Исправлено как на странице категории: `$tag_info['seo_title'] ?? ($tag_info['alt_title'] ?? $tag_info['title'])` |
| Пустой блок «Цветы» в фильтре: заголовок аккордеона выводился, под ним — ничего | `resources/views/components/product-tag-in-filter.blade.php` | Блок оборачивается в `@if ($tags->isNotEmpty())` — при пустом справочнике не показывается вовсе |
| `ProductTagSeeder` не вызывался из `DatabaseSeeder` → таблица `product_tags` всегда была пустой | `database/seeders/DatabaseSeeder.php` | Сидер добавлен в список вызовов и переписан на `updateOrCreate` по `slug` (идемпотентен) |
| Кэш списка тегов `product_tags_filter` не сбрасывался при правке тегов в админке (3600 сек) | `ProductTagInFilter`, `ProductTagListScreen`, `ProductTagEditScreen` | Добавлен `ProductTag::clearFilterCache()`, вызывается при сохранении и удалении тега, а также в сидере |
| Не было способа привязать товары к тегу: pivot `product_product_tag` существовал, но в админке поля для товаров не было | `ProductTagEditScreen` | Добавлен блок «Товары с этим тегом» (relation + `sync()`) |
| Частичное сохранение товара стирало все параметры: `save_info` всегда делал `sync()` по переданным значениям, а форма без блока параметров передавала пустоту | `ProductEditScreen::save_info()` | Добавлен скрытый маркер `product_options_present` в форму товара. Синхронизация выполняется только если маркер присутствует — тогда и снятие всех галочек работает (браузер не присылает пустые массивы), и частичное сохранение ничего не теряет |
| Устаревший route-кэш (`bootstrap/cache/routes-v7.php` от 28.10) не содержал новых админ-роутов | локальное окружение | Кэш очищен (`php artisan route:clear`). На проде — не забыть `php artisan route:cache` после деплоя |
| Устаревший config-кэш (`bootstrap/cache/config.php` от 28.10) не содержал нового `config/product_colors.php` → палитра не применялась, `meta` оставался пустым | локальное окружение | Кэш очищен (`php artisan config:clear`). На проде — обязательно `php artisan config:cache` после деплоя |

---

## 9. Что показала текущая база (13 товаров, 8 категорий, 11 праздников)

### 9.1 `products.consist` пуст у всех товаров

Ни у одного из 13 товаров JSON `consist` не заполнен. Значит:

- команда `products:sync-composition` **не нужна** — переносить нечего;
- «Состав» наполняется только вручную через админку;
- риска сломать YML-фид нет — он и сейчас отдаёт пустой состав.

**Бонус:** состав местами читается прямо из заголовка товара —
«Букет из Герберы», «Букет Герберы, Хризантемы», «Букет Эквадорская Роза»,
«Букет из розовых гипсофил». Это годится для **разового импорта** (чтобы не проставлять
состав руками 13 раз), но не как правило — заголовки свободный текст.

### 9.2 9 из 11 `celebrations` совпадают с новым списком «Событие»

| `celebrations` (11, сейчас в меню «Праздник») | Есть в новом справочнике «Событие» |
| ------------------------------------------------ | ------------------------------------- |
| 1 сентября, 8 марта, 14 февраля, 23 февраля, День матери, День рождения, День семьи, Татьянин день, Выпускной | да |
| День победы | нет (в новом списке — «9 мая») |
| Новый год | нет (в новом — «Новый год и Рождество») |

`celebrations` сегодня: меню «Праздник» в каталоге → SEO-страница `/celebrations/{slug}`
→ список товаров из pivot `celebration_product`.

**Риск решения «два независимых справочника»:** один и тот же товар придётся
проставлять дважды, и он попадёт либо в фильтр «Событие», либо на праздничную
страницу, либо в оба места — но связи разные, синхронизации нет.

**Варианты:**
- **а) Независимо** (текущее решение). Дёшево, но двойной ввод и риск рассинхрона.
- **б) Связать:** в `product_options` для типа `event` добавить nullable
  `celebration_id` → значение фильтра ссылается на существующий праздник. Одно
  действие в админке — и товар попадает и в фильтр, и на праздничную страницу.
  Миграции данных не требуется, `celebrations` не трогаем.
- **в) Заменить** `celebrations` новым справочником — миграция + правка меню,
  роутов и sitemap. Отвергли в разделе 7.

### 9.3 Пробелы в справочниках относительно текущих данных

| Что | Где | Комментарий |
| ---- | --- | ----------- |
| **Тюльпаны** | категория `tiulpany`, 2 товара | Тюльпанов **нет** в списке состава |
| **Розы Сантини** | в заголовках 4 товаров («Букет Герберы, Розы Сантини») | В списке есть «Кустовые розы», «Одноголовые розы», «Розы». Сантини — разновидность кустовой розы |
| **Цветы в коробках** | категория `cvety-v-korobkax`, 7 товаров | Двойной ввод: категория + Упаковка «Коробка» |
| **Сборные букеты из Роз** | категория `sbornye-bukety-iz-roz`, 4 товара | Двойной ввод: категория + Состав «Розы» |
| **Микс / Разноцветный** | в списке цветов | Не цвета, а мета-значения («букет ассорти», «разноцветный») |

---

## 10. Что реализовано (файлы)

### Созданы

| Файл | Назначение |
| ---- | ---------- |
| `database/migrations/2026_11_05_100000_create_product_option_types_table.php` | Типы параметров (`slug`, `title`, `position`, `show_in_filter`) |
| `database/migrations/2026_11_05_100100_create_product_options_table.php` | Значения параметров (`title`, `slug`, `image`, `external_id`, `meta`, `position`, `is_active`) |
| `database/migrations/2026_11_05_100200_create_product_product_option_table.php` | Связь товар ↔ значение (`quantity`, `sort`) |
| `app/Models/ProductOptionType.php` | Тип параметра, скоуп `filterable()` |
| `app/Models/ProductOption.php` | Значение параметра, скоупы `ofType()`, `active()`, автослаг |
| `app/Models/Concerns/HasProductOptions.php` | Relation `productOptions()` и `optionsOfType()` в `Product` |
| `app/Filters/ProductDimensions.php` | Список слагов измерений, участвующих в фильтре |
| `app/Support/ProductOptionCatalog.php` | Кэш справочника для фронта + сброс кэша из админки |
| `app/View/Components/ProductOptionsFilter.php` | Blade-компонент блока фильтра |
| `resources/views/components/product-options-filter.blade.php` | Разметка блока (аккордеон + чекбоксы) |
| `database/seeders/ProductOptionSeeder.php` | Идемпотентный сид 5 типов и 72 значений |
| `app/Orchid/Screens/ProductOption/ProductOptionListScreen.php` | Список значений справочника |
| `app/Orchid/Screens/ProductOption/ProductOptionEditScreen.php` | Создание/правка значения + массовое присвоение товарам |
| `app/Orchid/Layouts/ProductOption/ProductOptionListLayout.php` | Таблица справочника |
| `tests/Feature/ProductOptionsFilterTest.php` | 11 тестов на фильтр и админку |
| `tests/Feature/ProductTagAdminTest.php` | 3 теста на админку тегов и пустой блок «Цветы» |
| `config/product_colors.php` | Палитра оттенков параметра «Цвет» (21 значение) — источник истины для сидера, YML-фида и карточки |
| `tests/Feature/ProductOptionsFilterTest.php` | 18 тестов: частичное сохранение, кружок цвета, порядок блоков, меню, подписи |

### Изменены

| Файл | Что |
| ---- | --- |
| `app/Filters/QueryFilter.php` | Whitelist `$allowed`, `$reserved`, пропуск пустых значений, хук `applyExtra()`, метод `has()` |
| `app/Filters/ProductFilter.php` | `osobennosti` → ИЛИ; `applyExtra()` → `applyDimensions()` (мультивыбор по 5 измерениям) |
| `app/Models/Product.php` | Подключён трейт `HasProductOptions` |
| `app/Http/Controllers/CategoryController.php` | Фильтр на странице категории |
| `app/Http/Controllers/PriceController.php` | `$filter->has('order')` вместо `array_key_exists` |
| `app/Providers/OptionsProvider.php` | Шарится список типов параметров во все вьюхи |
| `app/Orchid/Screens/Product/ProductEditScreen.php` | 5 relation-полей параметров + скрытый маркер `product_options_present` + `sync()` + сброс кэша; починен `processingPlanId` |
| `resources/views/components/tovar-filter.blade.php` | Блоки параметров между «Стоимость» и «Цветы»; убрано мёртвое поле `brand` |
| `resources/views/tag.blade.php` | Утечка буферов + пустые `<title>`/`<meta description>` |
| `routes/platform.php` | Роуты `/admin/product-options` (список, создание, правка) |
| `tests/Feature/ProductFilterTest.php` | 7 тестов на починенный фильтр |
| `app/Models/ProductTag.php` | `clearFilterCache()` для сброса кэша списка тегов |
| `app/View/Components/ProductTagInFilter.php` | Использует `ProductTag::FILTER_CACHE_KEY` |
| `app/Orchid/Screens/ProductTag/ProductTagEditScreen.php` | Блок «Товары с этим тегом» + сброс кэша |
| `app/Orchid/Screens/ProductTag/ProductTagListScreen.php` | Сброс кэша при удалении тега |
| `database/seeders/ProductTagSeeder.php` | Переписан на `updateOrCreate` (идемпотентен) |
| `database/seeders/DatabaseSeeder.php` | Добавлен вызов `ProductTagSeeder` — раньше он не вызывался, и таблица тегов всегда была пустой |
| `resources/views/components/product-tag-in-filter.blade.php` | Блок не выводится, если тегов нет |
| `tests/Feature/PriceRangeTest.php` | Починена предпавшая сломанная проверка `class="tag_lnk"` |

### Как пользоваться

```bash
php artisan migrate                 # 3 новые таблицы
php artisan db:seed --class=ProductOptionSeeder   # 5 типов, 72 значения (идемпотентно)
```

Фильтр: `/catalog?color[]=8&packing[]=23&event[]=34` — внутри параметра ИЛИ, между параметрами И.

Админка:
- `/admin/product-options` — справочник значений (список, создание, правка, удаление);
- `/admin/products/{id}/edit` — блоки «Цвет», «Упаковка», «Кому», «Событие», «Состав»
  с поиском по названию (Orchid relation endpoint, `applyScope('ofType', ...)`);
- на экране значения параметра — блок «Товары с этим значением» для массового присвоения.

---

## 11. Оттенки цвета: где хранить

**Решение: `product_options.meta.hex`** (JSON-колонка, уже есть в схеме — миграция не нужна),
для «Микс» и «Разноцветный» — `meta.gradient` (CSS-градиент). Палитра хранится в коде
в `config/product_colors.php` и заливается в базу сидером.

Почему так, а не иначе:

| Вариант | Почему не выбран |
| ------- | ---------------- |
| Отдельная колонка `hex` в `product_options` | Таблица универсальная (5 типов), а колонка нужна только цвету. В `meta` уже можно хранить и `price_delta` для упаковки, и прочие атрибуты |
| Только в коде (конфиг), без базы | Пришлось бы деплоить ради смены оттенка; другие потребители (YML-фид для Яндекс.Маркета, карточка товара) читали бы конфиг, а не данные товара |
| Только вручную в админке | 21 значение × ручной ввод hex → гарантированные опечатки в оттенках; менеджеры на проде и так завалены рутиной |
| Отдельная таблица `product_colors` | Разрушает единый справочник параметров и возвращает вариант «таблица на каждый тип» |

Схема работы:

```
config/product_colors.php        — утверждённая палитра (код, ревьюится в git)
        │  сидер ProductOptionSeeder (заливает только при первом создании)
        ▼
product_options.meta = {"hex":"#D32F2F"} | {"gradient":"linear-gradient(...)"}
        │  читается при рендере фильтра, карточки товара, YML-фида
        ▼
кружок в фильтре: <i class="color_swatch" style="background: #D32F2F"></i>
```

Значения `meta` сидер **не перезаписывает**, если они уже заданы — правка оттенка
в админке переживает повторный запуск сидера.

Стиль кружка — `public/css/main.scss` (`.color_swatch`), рядом с уже существующими
правилами `.ch_label`. У «Белого» и «Кремового» есть тонкая рамка, иначе на белом фоне
кружок не виден.
