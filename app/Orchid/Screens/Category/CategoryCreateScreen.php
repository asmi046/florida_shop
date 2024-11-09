<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;

use Orchid\Screen\Screen;

use Orchid\Support\Color;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Picture;

use Orchid\Support\Facades\Toast;
use Orchid\Support\Facades\Layout;

class CategoryCreateScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

    public function query(): iterable
    {
        return [];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Создание категории';
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
            Layout::rows([

                Input::make('title')
                    ->title('Заголовок')
                    ->help('Заголовок категории')
                    ->required()
                    ->horizontal(),

                Input::make('showed_title')
                    ->title('Заголовок выводимый')
                    ->value($this->category->title)
                    ->help('Заголовок категории выводимый')
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->help('Slug категории')
                    ->horizontal(),

                Picture::make('img')->title('Изображение')->storage('public')->targetRelativeUrl(),

                Quill::make('description')->title('Описание'),

                Button::make('Добавить категорию')->method('save_info')->type(Color::SUCCESS())
            ])
        ];
    }

    public function save_info(Request $request) {

        $new_data = $request->validate([
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'img' => [],
            'showed_title' => [],
            'description' => []
        ]);


        Category::create($new_data);

        Toast::info("Категория добавлена");

        return redirect()->route('platform.category');
    }
}
