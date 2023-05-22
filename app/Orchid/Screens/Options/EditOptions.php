<?php

namespace App\Orchid\Screens\Options;

use Orchid\Screen\Screen;

use App\Models\Option;

use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

use Illuminate\Http\Request;

class EditOptions extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */

     public $option;

    public function query($id): iterable
    {
        return [
            "option" => Option::where('id',$id)->first()
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактирование опции: '.$this->option->name;
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

                Input::make('type')->type('hidden')->value($this->option->type),

                Input::make('name')
                    ->title('Имя опции')
                    ->value($this->option->name)
                    ->help('Описание опции')
                    ->disabled()
                    ->horizontal(),

                Input::make('title')
                    ->title('Описание')
                    ->value($this->option->title)
                    ->help('Описание опции')
                    ->disabled()
                    ->horizontal(),

                Quill::make('value')->required()->title('Значение опции')->value($this->option->value),

                Button::make('Сохранить')->method('save_info')->type(Color::SUCCESS())
            ])
        ];
    }

    public function save_info(Option $option, Request $request) {

        $new_data = $request->validate([
            'value' => ['required', 'string']
        ]);


        // dd($new_data, $option->type);

        if ($option->type === 'plan')
            $new_data["value"] = strip_tags($new_data["value"]);

        Option::where('id', $option->id)->update($new_data);
        Toast::info("Товар сохранен");
    }
}
