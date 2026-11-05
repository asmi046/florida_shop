<?php

namespace App\Orchid\Layouts\ProductOption;

use App\Models\ProductOption;
use App\Models\ProductOptionType;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductOptionListLayout extends Table
{
    /**
     * Data source.
     */
    protected $target = 'options';

    /**
     * Типы параметров для выпадающего фильтра.
     *
     * @return array<int, string>
     */
    protected function typeOptions(): array
    {
        return ProductOptionType::orderBy('position')
            ->pluck('title', 'id')
            ->all();
    }

    /**
     * Get the table cells to be displayed.
     */
    protected function columns(): iterable
    {
        return [
            TD::make('title', 'Название')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (ProductOption $option) {
                    $title = Link::make($option->title)
                        ->route('platform.product-options.edit', $option->id);

                    $swatch = $option->meta['gradient'] ?? ($option->meta['hex'] ?? null);

                    if (! $swatch) {
                        return $title;
                    }

                    return '<i class="color_swatch" style="background: '.e($swatch).'"></i> '.$title;
                }),

            TD::make('product_option_type_id', 'Тип')
                ->sort()
                ->filter(TD::FILTER_SELECT, $this->typeOptions())
                ->render(function (ProductOption $option) {
                    return $option->type?->title ?? '—';
                }),

            TD::make('slug', 'Слаг')
                ->filter(TD::FILTER_TEXT)
                ->render(function (ProductOption $option) {
                    return '<code>'.e($option->slug).'</code>';
                }),

            TD::make('products_count', 'Товаров')
                ->render(function (ProductOption $option) {
                    return $option->products_count;
                }),

            TD::make('position', 'Порядок')
                ->sort()
                ->width('100px'),

            TD::make('is_active', 'Активно')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (ProductOption $option) {
                    return $option->is_active
                        ? '<span class="text-success">да</span>'
                        : '<span class="text-muted">нет</span>';
                }),

            TD::make('actions', 'Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (ProductOption $option) {
                    return DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make('Редактировать')
                                ->route('platform.product-options.edit', $option->id)
                                ->icon('pencil'),

                            Button::make('Удалить')
                                ->icon('trash')
                                ->confirm('Удалить значение? Товары с этим значением останутся без параметра.')
                                ->method('remove', [
                                    'option' => $option->id,
                                ]),
                        ]);
                }),
        ];
    }
}
