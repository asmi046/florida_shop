<?php

namespace App\Orchid\Layouts\Review;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

use Illuminate\Support\Facades\Storage;

class ReviewsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'reviews';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [

                TD::make('avatar', 'Аватар')->width('15%')->render(
                    function($element) {

                        return "<img width='50' height='50' src='".($element->avatar?Storage::url('public/rewev_avatars/'.$element->avatar):asset("img/noPhoto.jpg"))."'>";
                    }
                ),

                TD::make('name', 'Имя клиента')->width('15%'),
                TD::make('lnk', 'Ссылка на отзыв')->width('15%')->render(
                    function($element) {
                        return "<a href='".$element->lnk."'>Посмотреть</a>";
                    }
                ),
                TD::make('text', 'Текст отзыва')->width('50%')

        ];
    }
}
