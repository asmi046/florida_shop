<?php

namespace App\Orchid\Screens\Options;

use Orchid\Screen\Screen;

use App\Models\Option;

use App\Orchid\Layouts\Options\OptionsTable;

class OptionsList extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "options" =>Option::paginate(25)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
        public function name(): ?string
    {
        return 'Основные опции сайта';
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
            OptionsTable::class
        ];
    }
}
