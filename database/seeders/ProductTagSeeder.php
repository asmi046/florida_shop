<?php

namespace Database\Seeders;

use App\Models\ProductTag;
use Illuminate\Database\Seeder;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::tags() as $tag) {
            ProductTag::updateOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }

        ProductTag::clearFilterCache();
    }

    /**
     * Рубрикатор «Цветы» для страниц тегов и блока фильтра.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function tags(): array
    {
        return [
            [
                'title' => 'Розы',
                'alt_title' => 'Букеты из Роз купить в Курске',
                'template' => null,
                'image' => null,
                'slug' => 'rozy',
                'description' => 'Прекрасные розы различных сортов и расцветок. Классический выбор для особых моментов и романтических подарков.',
                'seo_title' => 'Розы купить в Курске - свежие цветы с доставкой',
                'seo_description' => 'Купить розы в Курске с доставкой. Большой выбор свежих роз разных сортов и цветов. Быстрая доставка по городу.',
            ],
            [
                'title' => 'Герберы',
                'alt_title' => 'Букеты из Гербер купить в Курске',
                'template' => null,
                'image' => null,
                'slug' => 'gerbery',
                'description' => 'Яркие и солнечные герберы - отличный выбор для поднятия настроения. Долго стоят в вазе и радуют своими красками.',
                'seo_title' => 'Герберы купить в Курске - яркие цветы с доставкой',
                'seo_description' => 'Купить герберы в Курске с доставкой. Свежие яркие цветы разных оттенков. Быстрая доставка букетов из гербер.',
            ],
        ];
    }
}
