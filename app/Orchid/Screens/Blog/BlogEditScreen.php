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
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Actions\ModalToggle;


use Illuminate\Http\Request;

class BlogEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

    public $post;

    public function query($id): iterable
    {
        $post = BlogPost::where('id',$id)->first();
        return [
            "post" => $post
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование статьи: '.$this->post->title;
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
                    ->value($this->post->title)
                    ->help('Заголовок категории')
                    ->required()
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->value($this->post->slug)
                    ->help('Slug категории')
                    ->horizontal(),

                Quill::make('description')->required()->title('Описание')->value($this->post->description),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Основные поля'),

            Layout::rows([
                Input::make('seo_title')
                    ->title('SEO заголовок')
                    ->value($this->post->seo_title)
                    ->help('SEO заголовок')
                    ->horizontal(),

                TextArea::make('seo_description')
                    ->title('SEO описание')
                    ->value($this->post->seo_description)
                    ->help('SEO описание')
                    ->horizontal(),
                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('SEO поля'),

            Layout::rows([

                Picture::make('img')->required()->title('Загрузить основное изображение записи')->targetRelativeUrl()->value($this->post->img),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])->title('Изображения'),

        ];
    }

    public function save_info(BlogPost $post, Request $request) {

        $new_data = $request->validate([
            'title' => ['required', 'string'],
            'slug' => [],
            'description' => ['required', 'string'],
            'img' => ['required', 'string'],
            'seo_title' => [],
            'seo_description' => []
        ]);


        BlogPost::where('id', $post->id)->update($new_data);
        Toast::info("Статья сохранена");
    }
}
