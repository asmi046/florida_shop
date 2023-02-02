<?php

namespace App\Orchid\Layouts\Options;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Link;

class OptionsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'options';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id'),
            TD::make('name', 'Имя'),
            TD::make('title', 'Название'),
            TD::make('value', 'Контент')->render(function($element) {
                return  mb_strimwidth(strip_tags($element->value), 0, 30, "...");
            }),

            TD::make('action', 'Действие')->render(function($element) {
                return  Group::make([
                    Link::make('Редактировать')->route('platform.options_edit',$element->id)
                ]);
            })
        ];
    }
}
