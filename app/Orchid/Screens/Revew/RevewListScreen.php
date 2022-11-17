<?php

namespace App\Orchid\Screens\Revew;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

use App\Models\Review;

use App\Orchid\Layouts\Review\ReviewsTable;
use Orchid\Screen\Fields\Input;

use Orchid\Screen\Actions\ModalToggle;


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
            ModalToggle::make('Новый отзыв')->modal('addReviewModal')
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

            ReviewsTable::class,
            Layout::modal('addReviewModal',
                Layout::rows([
                    Input::make("zzz")
                ])->title("Создать новый отзыв")
            )
        ];
    }
}
