<?php

namespace App\Orchid\Screens\ProductOption;

use App\Models\ProductOption;
use App\Orchid\Layouts\ProductOption\ProductOptionListLayout;
use App\Support\ProductOptionCatalog;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ProductOptionListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): iterable
    {
        return [
            'options' => ProductOption::query()
                ->with('type')
                ->withCount('products')
                ->filters()
                ->defaultSort('position')
                ->defaultSort('title')
                ->paginate(),
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Параметры товаров';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Справочник значений параметров: цвет, упаковка, кому, событие, состав';
    }

    /**
     * Button commands.
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить значение')
                ->icon('plus')
                ->route('platform.product-options.create'),
        ];
    }

    /**
     * Views.
     */
    public function layout(): iterable
    {
        return [
            ProductOptionListLayout::class,
        ];
    }

    /**
     * Удалить значение.
     */
    public function remove(ProductOption $option): void
    {
        $option->delete();

        ProductOptionCatalog::flush();

        Toast::info('Значение удалено');
    }
}
