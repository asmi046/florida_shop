<?php

namespace Database\Seeders;

use App\Models\ProductOption;
use App\Models\ProductOptionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::dimensions() as $position => [$slug, $title, $values]) {
            $type = ProductOptionType::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'position' => $position + 1,
                    'show_in_filter' => true,
                ]
            );

            foreach ($values as $index => $valueTitle) {
                $slug = Str::slug($valueTitle);

                $option = ProductOption::firstOrNew([
                    'product_option_type_id' => $type->id,
                    'slug' => $slug,
                ]);

                $option->title = $valueTitle;
                $option->position = $index + 1;
                $option->is_active = true;

                // Оттенок заливается только если его ещё нет: правку,
                // сделанную в админке, повторный запуск сидера не затирает.
                if (empty($option->meta)) {
                    $option->meta = self::paletteFor($slug);
                }

                $option->save();
            }
        }
    }

    /**
     * Данные о цвете из палитры для значения параметра.
     *
     * @return array<string, string>|null
     */
    private static function paletteFor(string $slug): ?array
    {
        $palette = config('product_colors.'.$slug);

        if (! is_array($palette)) {
            return null;
        }

        return array_filter([
            'hex' => $palette['hex'] ?? null,
            'gradient' => $palette['gradient'] ?? null,
        ]);
    }

    /**
     * Типы параметров и их значения.
     *
     * @return array<int, array{0: string, 1: string, 2: array<int, string>}>
     */
    private static function dimensions(): array
    {
        return [
            [
                'composition',
                'Состав',
                [
                    'Альстромерии',
                    'Гвоздики (Диантусы)',
                    'Герберы',
                    'Гипсофила',
                    'Гортензии',
                    'Кустовые розы',
                    'Лилии',
                    'Маттиола',
                    'Одноголовые розы',
                    'Розы',
                    'Розы Сантини',
                    'Ромашки (Танацетум)',
                    'Тюльпаны',
                    'Хризантемы',
                ],
            ],
            [
                'packing',
                'Упаковка',
                [
                    'Сумка',
                    'Коробка',
                    'Бумага',
                ],
            ],
            [
                'color',
                'Цвет',
                [
                    'Бежевый',
                    'Белый',
                    'Бордовый',
                    'Голубой',
                    'Желтый',
                    'Зеленый',
                    'Коричневый',
                    'Красный',
                    'Кремовый',
                    'Малиновый',
                    'Микс',
                    'Мятный',
                    'Оранжевый',
                    'Персиковый',
                    'Пурпурный',
                    'Разноцветный',
                    'Розовый',
                    'Синий',
                    'Сиреневый',
                    'Фиолетовый',
                    'Черный',
                ],
            ],
            [
                'audience',
                'Кому',
                [
                    'Бабушке',
                    'Воспитателю',
                    'Коллеге',
                    'Мужчине',
                    'Подруге',
                    'Ребенку',
                    'Руководителю',
                    'Сестре',
                ],
            ],
            [
                'event',
                'Событие',
                [
                    '1 сентября',
                    '8 марта',
                    '9 мая',
                    '14 февраля',
                    '23 февраля',
                    'Выписка из роддома',
                    'Выпускной',
                    'Годовщина',
                    'Годовщина свадьбы',
                    'День рождения',
                    'День матери',
                    'День отца',
                    'День семьи',
                    'День учителя',
                    'Новоселье',
                    'Новый год и Рождество',
                    'Пасха',
                    'Повышение',
                    'Последний звонок',
                    'Рождение ребёнка',
                    'Свидание',
                    'Татьянин день',
                    'Торжество',
                    'Траурная флористика',
                    'Хэллоуин',
                    'Юбилей',
                ],
            ],
        ];
    }
}
