<?php

namespace App\Orchid\Layouts\Category;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;

use App\Models\Category;

use Orchid\Support\Color;

class CategoryListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'categories';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id'),
            TD::make('title', 'Заголовок'),
            TD::make('slug', 'Ссылка'),
            TD::make('description', 'Описание')->render(function($element) {
                return  mb_strimwidth(strip_tags($element->description), 0, 30, "...");
            }),

            TD::make('action', 'Действие')->render(function($element) {
                return  Group::make([
                    Link::make('Редактировать')->route('platform.category_edit',$element->id),
                    Button::make('Удалить')->method('delete_field', [$element->id])->type(Color::DANGER())
                ]);
            })

        ];
    }
}
