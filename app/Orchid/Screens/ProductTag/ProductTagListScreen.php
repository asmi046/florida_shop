<?php

namespace App\Orchid\Screens\ProductTag;

use App\Models\ProductTag;
use App\Orchid\Layouts\ProductTag\ProductTagListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class ProductTagListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): iterable
    {
        return [
            'tags' => ProductTag::filters()
                ->defaultSort('id', 'desc')
                ->paginate()
        ];
    }

    /**
     * Display header name.
     */
    public function name(): ?string
    {
        return 'Теги продуктов';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Управление тегами для категоризации продуктов';
    }

    /**
     * Button commands.
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Создать тег')
                ->icon('plus')
                ->route('platform.product-tags.create'),
        ];
    }

    /**
     * Views.
     */
    public function layout(): iterable
    {
        return [
            ProductTagListLayout::class
        ];
    }

    /**
     * Remove tag
     */
    public function remove(ProductTag $tag): void
    {
        $tag->delete();

        Toast::info('Тег удален успешно');
    }
}
