<?php

namespace App\Orchid\Screens\Blog;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

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
        $post = BlogPost::findOrFail($id);

        return [
            'post' => $post,
        ];
    }

    /**
     * Display header name.
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
                    ->help('Заголовок статьи')
                    ->required()
                    ->maxlength(255)
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->value($this->post->slug)
                    ->help('Slug статьи; будет создан из заголовка, если оставить пустым')
                    ->maxlength(255)
                    ->horizontal(),

                Input::make('short_description')
                    ->title('Краткое описание')
                    ->value($this->post->short_description)
                    ->required()
                    ->maxlength(255)
                    ->horizontal(),

                DateTimer::make('published_at')
                    ->title('Дата публикации')
                    ->value($this->post->published_at)
                    ->enableTime()
                    ->format24hr()
                    ->serverFormat()
                    ->required()
                    ->horizontal(),

                Quill::make('description')
                    ->required()
                    ->title('Описание')
                    ->value($this->post->description),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS()),
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
            ])->title('SEO поля'),

            Layout::rows([

                Picture::make('img')->required()->title('Загрузить основное изображение записи')->targetRelativeUrl()->value($this->post->img),

            ])->title('Изображения'),

        ];
    }

    public function save_info(Request $request, int $id)
    {
        $post = BlogPost::findOrFail($id);

        if (blank($request->input('slug')) && is_string($request->input('title'))) {
            $request->merge(['slug' => Str::slug($request->input('title'))]);
        }

        $new_data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($post->id)],
            'short_description' => ['required', 'string', 'max:255'],
            'published_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'img' => ['required', 'string'],
            'seo_title' => ['nullable', 'string'],
            'seo_description' => ['nullable', 'string'],
        ]);

        $post->fill($new_data)->save();
        Toast::info('Статья сохранена');
    }
}
