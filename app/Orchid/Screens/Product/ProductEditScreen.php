<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\Product;
use App\Models\Category;

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

use Illuminate\Http\Request;

class ProductEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $product;
     public $product_cat;

    public function query($id): iterable
    {
        $product = Product::where('id',$id)->first();
        $cat = $product->tovar_categories;
        return [
            "product" => $product,
            "product_cat"=> $cat
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование продукта: '.$this->product->title;
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
            Layout::rows([

                Input::make('sku')
                    ->title('Артикул')
                    ->value($this->product->sku)
                    ->help('Артикул уникальный для каждого товара')
                    ->horizontal(),

                Input::make('title')
                    ->title('Заголовок')
                    ->value($this->product->title)
                    ->help('Заголовок категории')
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->value($this->product->slug)
                    ->help('Slug категории')
                    ->horizontal(),

                Input::make('price')
                    ->title('Цена')
                    ->value($this->product->price)
                    ->help('Действующая цена')
                    ->horizontal(),

                Input::make('old_price')
                    ->title('Старая цена')
                    ->value($this->product->old_price)
                    ->help('Цена до скидки')
                    ->horizontal(),

                Input::make('sales_count')
                    ->title('Количество продаж')
                    ->value($this->product->old_price)
                    ->help('Цена до скидки')
                    ->horizontal(),

                Switcher::make('hit')
                    ->value($this->product->hit)
                    ->sendTrueOrFalse()
                    ->title('Хит продаж (hit)')
                    ->placeholder('Пометка hit')
                    ->help('Пометка hit')->horizontal(),

                Switcher::make('new')
                    ->value($this->product->new)
                    ->sendTrueOrFalse()
                    ->title('Хит продаж (new)')
                    ->placeholder('Пометка new')
                    ->help('Пометка new ')->horizontal(),


                Relation::make('categories.')
                    ->fromModel(Category::class, 'title', 'id')
                    ->title('Категории товара')
                    ->value($this->product_cat)
                    ->multiple()
                    ->help('Выберите категорию'),


                Quill::make('description')->title('Описание')->value($this->product->description),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Основные поля'),

            Layout::rows([
                Input::make('seo_title')
                    ->title('SEO заголовок')
                    ->value($this->product->seo_title)
                    ->help('SEO заголовок')
                    ->horizontal(),

                TextArea::make('seo_description')
                    ->title('SEO описание')
                    ->value($this->product->seo_description)
                    ->help('SEO описание')
                    ->horizontal(),
                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('SEO поля'),

            Layout::rows([

                Picture::make('img')->title('Загрузить основное изображение записи')->targetRelativeUrl()->value($this->product->img),
                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Изображения')
        ];
    }

    public function save_info(Product $product, Request $request) {

        $new_data = $request->validate([
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'description' => ['string']
        ]);


        Product::where('id', $product->id)->update($new_data);
        Toast::info("Продукт сохранен");
    }
}
