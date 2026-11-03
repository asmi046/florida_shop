<?php

namespace App\Orchid\Support;

use Orchid\Screen\Fields\Input;

class ProcessingPlanField
{
    /**
     * Поле ввода ID техкарты (processingplan) МойСклад.
     *
     * Значение вводится вручную: UUID техкарты из МойСклад.
     */
    public static function make(?string $value = null): Input
    {
        return Input::make('processingPlanId')
            ->title('ID техкарты МойСклад')
            ->value($value)
            ->help('UUID техкарты (processingplan) из МойСклад. Проставляется автоматически командой moysklad:processing-plans --sync')
            ->horizontal();
    }
}
