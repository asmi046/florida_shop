<?php

namespace App\Orchid\Screens\Product;

use Orchid\Screen\Screen;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Celebration;

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

class ProductEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $product;
     public $product_cat;
     public $product_cel;
     public $product_img;

    public function query($id): iterable
    {
        $product = Product::where('id',$id)->first();
        $cat = $product->tovar_categories;
        $cel = $product->tovar_celebration;
        $img = $product->product_images;
        return [
            "product" => $product,
            "product_cat"=> $cat,
            "product_cel"=> $cel,
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

                Switcher::make('asc_nal')
                    ->value($this->product->asc_nal)
                    ->sendTrueOrFalse()
                    ->title('Уточнить наличие')
                    ->placeholder('Уточнить наличие')
                    ->help('Разрешить продажу или запросить уточнение')->horizontal(),

                Input::make('sku')
                    ->title('Артикул')
                    ->value($this->product->sku)
                    ->help('Артикул уникальный для каждого товара')
                    ->required()
                    ->horizontal(),

                Input::make('title')
                    ->title('Заголовок')
                    ->value($this->product->title)
                    ->help('Заголовок категории')
                    ->required()
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->value($this->product->slug)
                    ->help('Slug категории')
                    ->horizontal(),

                Input::make('height')
                    ->title('Высота')
                    ->value($this->product->height)
                    ->help('Высота букета')
                    ->horizontal(),

                Input::make('radius')
                    ->title('Диаметр')
                    ->value($this->product->radius)
                    ->help('Диаметр букета')
                    ->horizontal(),

                Input::make('price')
                    ->title('Цена')
                    ->value($this->product->price)
                    ->help('Действующая цена')
                    ->required()
                    ->horizontal(),

                Input::make('old_price')
                    ->title('Старая цена')
                    ->value($this->product->old_price)
                    ->help('Цена до скидки')
                    ->horizontal(),

                Input::make('sales_count')
                    ->title('Количество продаж')
                    ->value($this->product->sales_count)
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


                Relation::make('category.')
                    ->fromModel(Category::class, 'title', 'id')
                    ->title('Категории товара')
                    ->value($this->product_cat)
                    ->multiple()
                    ->help('Выберите категорию'),

                Relation::make('сelebration.')
                    ->fromModel(Celebration::class, 'title', 'id')
                    ->title('Праздники')
                    ->value($this->product_cel)
                    ->multiple()
                    ->help('Выберите праздник'),


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

                Picture::make('img')->title('Загрузить основное изображение записи')->storage('public')->targetRelativeUrl()->value($this->product->img),

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

        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Изображение удалено");
            // Alert::info("Изображение удалено");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }

    public function save_info(Product $product, Request $request) {

        // dd($request->get("category"));

        $new_data = $request->validate([
            'sku' => ['required', 'string',  Rule::unique('products')->ignore($product->id)],
            'title' => ['required', 'string'],
            'slug' => [],
            'img' => [],
            'description' => [],
            'price' => ['required'],
            'old_price' => [],
            'sales_count' => [],
            'hit' => [],
            'new' => [],
            'asc_nal' => [],
            'height' => [],
            'radius' => [],
            'seo_title' => [],
            'seo_description' => [],
        ]);

        $product->tovar_categories()->sync($request->get("category"));
        $product->tovar_celebration()->sync($request->get("сelebration"));

        Product::where('id', $product->id)->update($new_data);
        Toast::info("Продукт сохранен");
    }
}
