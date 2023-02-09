<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use App\Models\ProductImage;


class ProductImageTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'product_img';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [

            TD::make('id', 'id'),
            TD::make('link', 'Фото')->render(
                function($element) {
                    return "<img width='50' height='50' src='".($element->link?$element->link:asset("img/noPhoto.jpg"))."'>";
                }
            ),
            TD::make('alt', 'Alt'),
            TD::make('title', 'Title'),

            TD::make('action', 'Действие')->render(function($element) {
                return  Group::make([
                    Button::make('Удалить')->method('delete_image')
                    ->parameters([
                        'id' => $element->id
                    ])->type(Color::DANGER())
                ]);
            })

        ];
    }
}
