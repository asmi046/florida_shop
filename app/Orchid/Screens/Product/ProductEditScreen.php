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


use App\Orchid\Layouts\Product\ProductImageTable;

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
     public $product_img;

    public function query($id): iterable
    {
        $product = Product::where('id',$id)->first();
        $cat = $product->tovar_categories;
        $img = $product->product_images;
        return [
            "product" => $product,
            "product_cat"=> $cat,
            "product_img" => $img
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
            ])->title('Изображения'),

            ProductImageTable::class,


            Layout::rows([

                ModalToggle::make('Добавить изображение')
                ->modal('ImgLoadModal')
                ->method('load_image')
                ->icon('picture')
                ->modalTitle('Добавить изображение'),

            ])->title('Управление изображениями товара'),
        ];
    }



    public function load_image(Product $product, Request $request) {
        // dd($request->all());

        $new_data = $request->validate([
            'link' => ['required', 'string'],
            'alt' => [],
            'title' => [],
        ]);

        $product->product_images()->create($new_data);

        Toast::info("Изображение добавлено");
    }

    public function delete_image(Request $request) {
        $dell_elem = ProductImage::where('id', $request->input("id"))->first();

        // dd($dell_elem, $request->input("id"));

        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Изображение удалено");
        } else {
            Toast::info("Ошибка при удалении");
        }
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
