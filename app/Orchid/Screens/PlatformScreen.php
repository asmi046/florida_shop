<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

use App\Models\Review;
use App\Models\Category;
use App\Models\Product;

class PlatformScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'metrics' => [
                'rew'    => ['value' => Review::all()->count()],
                'tovars' => ['value' => Product::all()->count()],
                'categorys'   => ['value' => Category::all()->count()],
            ],

        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Florida 46';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Магазин цветов в Курске Florida 46';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Перейти на сайт')
                ->href(route("home"))
                ->icon('globe-alt'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Отзывов'    => 'metrics.rew',
                'Товаров' => 'metrics.tovars',
                'Категорий' => 'metrics.categorys',
            ]),
        ];
    }
}
