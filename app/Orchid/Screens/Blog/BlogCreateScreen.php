<?php

namespace App\Orchid\Screens\Blog;

use Orchid\Screen\Screen;

use App\Models\BlogPost;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Picture;

use Illuminate\Http\Request;

class BlogCreateScreen extends Screen
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
        return 'Создание статьи';
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

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->help('Slug категории')

                    ->horizontal(),

                Quill::make('description')->required()->title('Описание'),

                Button::make('Добавить статьи')->method('save_info')->type(Color::SUCCESS())
            ])->title('Основные поля'),

            Layout::rows([
                Input::make('seo_title')
                    ->title('SEO заголовок')
                    ->help('SEO заголовок')
                    ->horizontal(),

                TextArea::make('seo_description')
                    ->title('SEO описание')
                    ->help('SEO описание')
                    ->horizontal(),
                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('SEO поля'),

            Layout::rows([

                Picture::make('img')->required()->title('Загрузить основное изображение записи')->targetRelativeUrl(),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Изображения'),
        ];
    }

    public function save_info(Request $request) {

        $new_data = $request->validate([
            'title' => ['required', 'string'],
            'slug' => [],
            'description' => ['required', 'string'],
            'img' => ['required', 'string'],
            'seo_title' => [],
            'seo_description' => []
        ]);


        BlogPost::create($new_data);

        Toast::info("Статья добавлена");

        return redirect()->route('platform.blog');
    }
}
