<?php

namespace App\Orchid\Screens\Category;

use Orchid\Screen\Screen;

use App\Models\Category;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use Illuminate\Http\Request;

class CategoryEditScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $category;

    public function query($id): iterable
    {
        return [
            "category" => Category::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование категории: '.$this->category->title;
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
                    ->value($this->category->title)
                    ->help('Заголовок категории')
                    ->required()
                    ->horizontal(),

                Input::make('slug')
                    ->title('Окончание ссылки')
                    ->value($this->category->slug)
                    ->help('Slug категории')
                    ->horizontal(),

                Quill::make('description')->required()->title('Описание')->value($this->category->description),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])
        ];
    }

    public function save_info(Category $category, Request $request) {

        $new_data = $request->validate([
            'title' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'description' => ['string']
        ]);


        Category::where('id', $category->id)->update($new_data);
        Toast::info("Категория сохранена");
    }
}
