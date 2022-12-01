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
use Orchid\Screen\Fields\Picture;
use Orchid\Attachment\Models\Attachment;

use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Toast;

use Illuminate\Support\Facades\Storage;

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
            ModalToggle::make('Новый отзыв')->modal('addReviewModal')->method('newReview')
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
                    Picture::make('avatar')->title('Загрузить аватар')->targetRelativeUrl(),
                ])
            )->title("Создать новый отзыв"),

            Layout::modal('editReviewModal',
                Layout::rows([
                    Input::make("review.id")->type('hidden'),
                    Group::make([
                        Input::make("review.name")->required()->title('Имя клиента'),
                        Input::make("review.lnk")->required()->title('Ссылка на отзыв в соцсетях'),
                    ]),
                    TextArea::make("review.text")->required()->title('Текст отзыва'),
                    Picture::make('review.avatar')->title('Загрузить аватар')->targetRelativeUrl(),
                ])
            )->async('asyncGetClient'),

            ReviewsTable::class,

        ];
    }

    public function asyncGetClient(Review $review) {
        return [
            'review' => $review
        ];
    }

    public function newReview(Request $request) {
        $request->validate([
            'name' => ['required'],
            'lnk' => ['required'],
            'text' => ['required', 'min:5'],
        ]);


        $review = Review::create($request->all());

        $attach = Attachment::where('name',  pathinfo($request->input('avatar'))['filename'])->first();
        if ($attach) $attach->delete();

        Toast::info("Отзыв добавлен");
    }

    public function editReview(Request $request) {
        Review::find($request->input('review.id'))->update($request->review);
        Toast::info("Отзыв обновлен");
    }

    public function deleteReview(Request $request) {
        Storage::delete($request->input('avatar'));
        Review::find($request->input('id'))->delete($request->input('id'));
        Toast::info("Отзыв удален");
    }
}
