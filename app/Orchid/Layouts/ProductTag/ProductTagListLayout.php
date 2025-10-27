<?php

namespace App\Orchid\Layouts\ProductTag;

use Orchid\Screen\TD;
use App\Models\ProductTag;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

class ProductTagListLayout extends Table
{
    /**
     * Data source.
     */
    protected $target = 'tags';

    /**
     * Get the table cells to be displayed.
     */
    protected function columns(): iterable
    {
        return [
            TD::make('title', 'Заголовок')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (ProductTag $tag) {
                    return Link::make($tag->title)
                        ->route('platform.product-tags.edit', $tag->id);
                }),

            TD::make('alt_title', 'Альт. заголовок')
                ->filter(TD::FILTER_TEXT)
                ->width('200px')
                ->render(function (ProductTag $tag) {
                    return Str::limit($tag->alt_title, 40);
                }),

            TD::make('slug', 'Слаг')
                ->filter(TD::FILTER_TEXT)
                ->render(function (ProductTag $tag) {
                    return '<code>' . $tag->slug . '</code>';
                }),

            TD::make('products_count', 'Товаров')
                ->render(function (ProductTag $tag) {
                    return $tag->products()->count();
                }),

            TD::make('created_at', 'Создан')
                ->sort()
                ->render(function (ProductTag $tag) {
                    return $tag->created_at->format('d.m.Y H:i');
                }),

            TD::make('actions', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (ProductTag $tag) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make('Редактировать')
                                ->route('platform.product-tags.edit', $tag->id)
                                ->icon('pencil'),

                            Button::make('Удалить')
                                ->icon('trash')
                                ->confirm('Вы уверены, что хотите удалить этот тег?')
                                ->method('remove', [
                                    'tag' => $tag->id,
                                ]),
                        ]);
                }),
        ];
    }
}
