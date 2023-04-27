<?php

namespace App\Orchid\Screens\Blog;

use Orchid\Screen\Screen;

use App\Models\BlogPost;

use App\Orchid\Layouts\Blog\BlogListTable;

use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;
use Orchid\Support\Facades\Toast;

class BlogListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "posts" => BlogPost::orderByDesc("created_at")->paginate(15)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Блог';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Добавить статью')->route('platform.blog_create')->type(Color::SUCCESS())
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
            BlogListTable::class
        ];
    }


    public function delete_field($id) {
        $dell_elem = BlogPost::where('id', $id)->first();
        if ($dell_elem ) {
            $dell_elem->delete();
            Toast::info("Статья удалена");
        } else {
            Toast::info("Ошибка при удалении");
        }
    }
}
