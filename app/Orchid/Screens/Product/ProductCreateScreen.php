<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Validation\Rule;

use App\Orchid\Layouts\Product\ProductImageTable;

use Illuminate\Http\Request;

class ProductCreateScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

    public function query(): iterable
    {
        return [];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создание товара';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('ImgLoadModal', [
                Layout::rows([
                    Picture::make('link')->title('Загрузить основное изображение')->targetRelativeUrl(),

                    Input::make('alt')
                        ->title('alt изображения'),

                    Input::make('title')
                        ->title('title изображения')
                ]),
            ]),

            Layout::rows([

                Input::make('sku')
                    ->title('Артикул')
                    ->help('Артикул уникальный для каждого товара')
                    ->required()
                    ->horizontal(),

                Input::make('title')
                    ->title('Заголовок')
                    ->help('Заголовок категории')
                    ->required()
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->help('Slug категории')
                    ->horizontal(),


                Input::make('height')
                    ->title('Высота')
                    ->help('Высота букета')
                    ->horizontal(),

                Input::make('radius')
                    ->title('Диаметр')
                    ->help('Диаметр букета')
                    ->horizontal(),

                Input::make('price')
                    ->title('Цена')
                    ->help('Действующая цена')
                    ->required()
                    ->horizontal(),

                Input::make('old_price')
                    ->title('Старая цена')
                    ->help('Цена до скидки')
                    ->horizontal(),

                Input::make('sales_count')
                    ->title('Количество продаж')
                    ->help('Цена до скидки')
                    ->horizontal(),

                Switcher::make('hit')
                    ->sendTrueOrFalse()
                    ->title('Хит продаж (hit)')
                    ->placeholder('Пометка hit')
                    ->help('Пометка hit')->horizontal(),

                Switcher::make('new')
                    ->sendTrueOrFalse()
                    ->title('Хит продаж (new)')
                    ->placeholder('Пометка new')
                    ->help('Пометка new ')->horizontal(),


                Relation::make('category.')
                    ->fromModel(Category::class, 'title', 'id')
                    ->title('Категории товара')
                    ->multiple()
                    ->help('Выберите категорию'),


                Quill::make('description')->title('Описание'),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Основные поля'),

            Layout::rows([
                Input::make('seo_title')
                    ->title('SEO заголовок')
                    ->help('SEO заголовок')
                    ->horizontal(),

                TextArea::make('seo_description')
                    ->title('SEO описание')
                    ->help('SEO описание')
                    ->horizontal(),
                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('SEO поля'),

            Layout::rows([

                Picture::make('img')->title('Загрузить основное изображение записи')->targetRelativeUrl(),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Изображения'),



        ];
    }

    public function save_info(Request $request) {

        $new_data = $request->validate([
            'sku' => ['required', 'string',  Rule::unique('products')->ignore(Product::class)],
            'title' => ['required', 'string'],
            'slug' => [],
            'img' => [],
            'description' => [],
            'price' => ['required'],
            'old_price' => [],
            'sales_count' => [],
            'hit' => [],
            'new' => [],
            'height' => [],
            'radius' => [],
            'seo_title' => [],
            'seo_description' => [],
        ]);


        $new_tovar = Product::create($new_data);

        $new_tovar->tovar_categories()->sync($request->get("category"));

        Toast::info("Товар добавлен");

        return redirect()->route('platform.product');
    }
}
