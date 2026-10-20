<?php

namespace App\Orchid\Layouts\Blog;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class BlogListTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'posts';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'id')->sort(),
            TD::make('img', 'Фото')->render(
                function ($element) {
                    return "<img width='50' height='50' src='".($element->img ? $element->img : asset('img/noPhoto.jpg'))."'>";
                }
            ),
            TD::make('title', 'Заголовок')->sort()->filter(TD::FILTER_TEXT),
            TD::make('short_description', 'Краткое описание')->render(function ($element) {
                return mb_strimwidth(strip_tags($element->short_description), 0, 50, '...');
            }),
            TD::make('published_at', 'Дата публикации')->render(function ($element) {
                return $element->published_at?->format('d.m.Y H:i');
            })->sort(),
            TD::make('description', 'Описание')->render(function ($element) {
                return mb_strimwidth(strip_tags($element->description), 0, 30, '...');
            }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn ($element) => DropDown::make()
                    ->icon('options-vertical')
                    ->list([
                        Link::make('Редактировать')
                            ->route('platform.blog_edit', $element->id)
                            ->icon('pencil'),
                        Button::make('Удалить')
                            ->icon('trash')
                            ->confirm(__('Данная статья будет удалена навсегда! Вы согласны?'))
                            ->method('delete_field', ['id' => $element->id]),
                    ])),

        ];
    }
}
