<?php

namespace App\Orchid\Screens\Revew;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

use App\Models\Review;

use App\Orchid\Layouts\Review\ReviewsTable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Upload;
use Orchid\Attachment\Models\Attachment;

use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Toast;

use Illuminate\Http\Request;

class RevewListScreen extends Screen
{
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'reviews' => Review::paginate(15)
        ];
    }

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Отзывы на магазин';
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Новый отзыв')->modal('addReviewModal')->method('action')
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
            Layout::modal('addReviewModal',
                Layout::rows([
                    Group::make([
                        Input::make("name")->required()->title('Имя клиента'),
                        Input::make("lnk")->required()->title('Ссылка на отзыв в соцсетях'),
                    ]),


                    TextArea::make("text")->required()->title('Текст отзыва'),
                    Upload::make('avatar')->title('Загрузить аватар')->groups('photo'),
                ])
            )->title("Создать новый отзыв"),
            ReviewsTable::class,

        ];
    }

    public function action(Request $request) {
        $request->validate([
            'name' => ['required'],
            'lnk' => ['required'],
            'text' => ['required', 'min:50'],
        ]);

        Review::create($request->all());

        Toast::info("все ок");
    }
}
