<?php

namespace App\Orchid\Screens\ProductOption;

use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionType;
use App\Support\ProductOptionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductOptionEditScreen extends Screen
{
    /**
     * @var ProductOption
     */
    public $option;

    /**
     * Query data.
     */
    public function query(ProductOption $option): iterable
    {
        return [
            'option' => $option,
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return $this->option->exists
            ? 'Редактировать значение: '.$this->option->title
            : 'Новое значение параметра';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Значения параметров используются в фильтре каталога';
    }

    /**
     * Button commands.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Сохранить')
                ->icon('check')
                ->method('save'),

            Button::make('Удалить')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->option->exists)
                ->confirm('Удалить значение? Товары с этим значением останутся без параметра.'),
        ];
    }

    /**
     * Views.
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('option.product_option_type_id')
                    ->fromModel(ProductOptionType::class, 'title', 'id')
                    ->title('Тип параметра')
                    ->value($this->option->product_option_type_id)
                    ->required()
                    ->help('Цвет, Упаковка, Кому, Событие или Состав'),

                Input::make('option.title')
                    ->title('Название')
                    ->value($this->option->title)
                    ->placeholder('Красный, Коробка, 8 марта')
                    ->required(),

                Input::make('option.slug')
                    ->title('Слаг')
                    ->value($this->option->slug)
                    ->help('Оставьте пустым — сгенерируется из названия'),

                Input::make('option.position')
                    ->type('number')
                    ->title('Порядок')
                    ->value($this->option->position ?? 0)
                    ->min(0)
                    ->help('Порядок вывода значения в фильтре'),

                Switcher::make('option.is_active')
                    ->title('Активно')
                    ->sendTrueOrFalse()
                    ->value($this->option->exists ? $this->option->is_active : true)
                    ->help('Неактивные значения не показываются в фильтре'),

                Input::make('option.external_id')
                    ->title('Внешний идентификатор')
                    ->value($this->option->external_id)
                    ->help('Идентификатор значения в МойСклад, если применимо'),

                TextArea::make('option.meta')
                    ->title('Дополнительные данные (JSON)')
                    ->rows(3)
                    ->value($this->option->meta
                        ? json_encode($this->option->meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                        : '')
                    ->help('Необязательно. Например: {"hex": "#e53935"}'),

                Button::make('Сохранить')->method('save')->icon('check'),
            ])->title('Значение параметра'),

            Layout::rows([
                Relation::make('products.')
                    ->fromModel(Product::class, 'title', 'id')
                    ->title('Товары с этим значением')
                    ->value($this->option->exists ? $this->option->products : [])
                    ->multiple()
                    ->help('Массовое присвоение значения товарам'),

                Button::make('Сохранить')->method('save')->icon('check'),
            ])->title('Товары'),
        ];
    }

    /**
     * Сохранить значение.
     */
    public function save(Request $request, ProductOption $option): void
    {
        $data = $request->validate([
            'option.title' => ['required', 'string', 'max:255'],
            'option.slug' => ['nullable', 'string', 'max:255'],
            'option.product_option_type_id' => ['required', 'integer', Rule::exists('product_option_types', 'id')],
            'option.position' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'option.external_id' => ['nullable', 'string', 'max:255'],
            'option.meta' => ['nullable', 'string', 'json'],
            'option.is_active' => ['nullable', 'boolean'],
        ]);

        $typeId = (int) $data['option']['product_option_type_id'];
        $slug = Str::slug($data['option']['slug'] ?: $data['option']['title']);

        $duplicate = ProductOption::query()
            ->where('product_option_type_id', $typeId)
            ->where('slug', $slug)
            ->when($option->exists, fn ($query) => $query->where('id', '!=', $option->id))
            ->exists();

        if ($duplicate) {
            Toast::error('В этом типе параметра уже есть значение с таким названием');

            return;
        }

        $data['option']['slug'] = $slug;
        $data['option']['meta'] = isset($data['option']['meta'])
            ? json_decode($data['option']['meta'], true)
            : null;

        $option->fill($data['option'])->save();

        if ($request->has('products')) {
            $option->products()->sync($request->get('products', []));
        }

        ProductOptionCatalog::flush();

        Toast::info('Значение параметра сохранено');
    }

    /**
     * Удалить значение.
     */
    public function remove(ProductOption $option): void
    {
        $option->delete();

        ProductOptionCatalog::flush();

        Toast::info('Значение удалено');
    }
}
