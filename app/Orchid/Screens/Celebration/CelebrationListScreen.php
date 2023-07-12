<?php

namespace App\Orchid\Screens\Celebration;

use Orchid\Screen\Screen;

use App\Models\Celebration;
use App\Orchid\Layouts\Celebration\CelebrationListTable;

use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class CelebrationListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "categories" => Celebration::orderByDesc("created_at")->paginate(15)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Праздники';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить праздник')->route('platform.celebration_create')->type(Color::SUCCESS())
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            CelebrationListTable::class
        ];
    }


    public function delete_field($id) {
        $dell_elem = Celebration::where('id', $id)->first();
        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Праздник удален");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }
}
