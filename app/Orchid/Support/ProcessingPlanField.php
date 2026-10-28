<?php

namespace App\Orchid\Support;

use Illuminate\Support\Facades\File;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class ProcessingPlanField
{
    /**
     * Имя файла выгрузки техкарт в public/.
     */
    public const EXPORT_FILE = 'moysklad_processing_plans.json';

    /**
     * Поле выбора ID техкарты (processingplan) МойСклад.
     *
     * Если есть выгрузка техкарт — это выпадающий список с названиями,
     * иначе — обычный текстовый ввод UUID.
     */
    public static function make(?string $value = null): Input|Select
    {
        $options = self::options();

        if (empty($options)) {
            return Input::make('processingPlanId')
                ->title('ID техкарты МойСклад')
                ->value($value)
                ->help('UUID техкарты (processingplan) из МойСклад. Проставляется автоматически командой moysklad:processing-plans --sync')
                ->horizontal();
        }

        if (! empty($value) && ! array_key_exists($value, $options)) {
            $options[$value] = $value.' (нет в выгрузке)';
        }

        return Select::make('processingPlanId')
            ->title('Техкарта МойСклад')
            ->options($options)
            ->empty('— не выбрана —')
            ->value($value)
            ->help('UUID техкарты (processingplan) из МойСклад')
            ->horizontal();
    }

    /**
     * Список техкарт из файла выгрузки: id => название.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $path = public_path(self::EXPORT_FILE);

        if (! File::exists($path)) {
            return [];
        }

        $data = json_decode(File::get($path), true);

        $options = [];

        foreach ($data['plans'] ?? [] as $plan) {
            $id = $plan['id'] ?? null;

            if ($id === null) {
                continue;
            }

            $options[$id] = $plan['name'] ?? $id;
        }

        uasort($options, fn ($a, $b) => strnatcasecmp((string) $a, (string) $b));

        return $options;
    }
}
