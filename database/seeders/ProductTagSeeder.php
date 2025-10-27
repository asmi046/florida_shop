<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'title' => 'Розы',
                'alt_title' => 'Букеты из Роз купить в Курске',
                'template' => null,
                'image' => null,
                'slug' => 'rozy',
                'description' => 'Прекрасные розы различных сортов и расцветок. Классический выбор для особых моментов и романтических подарков.',
                'seo_title' => 'Розы купить в Курске - свежие цветы с доставкой',
                'seo_description' => 'Купить розы в Курске с доставкой. Большой выбор свежих роз разных сортов и цветов. Быстрая доставка по городу.',
                'created_at' => now(),
                'updated_at' => now(),
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
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('product_tags')->insert($tags);
    }
}
