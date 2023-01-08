<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;

use DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("categories")->insert(
            [
                [
                    'title' => "Сборные букеты", 'slug' => Str::slug("Сборные букеты"),
                ],

                [
                    'title' => "Цветы в коробках", 'slug' => Str::slug("Цветы в коробках"),
                ],

                [
                    'title' => "Сборные букеты из Роз", 'slug' => Str::slug("Сборные букеты из Роз"),
                ],

                [
                    'title' => "Свадебные букеты", 'slug' => Str::slug("Свадебные букеты"),
                ],

                [
                    'title' => "Монобукеты", 'slug' => Str::slug("Монобукеты"),
                ],

                [
                    'title' => "Необычьные", 'slug' => Str::slug("Необычьные"),
                ],

                [
                    'title' => "Тюльпаны", 'slug' => Str::slug("Тюльпаны"),
                ],

                [
                    'title' => "Подарки и игрушки", 'slug' => Str::slug("Подарки и игрушки"),
                ],
            ]
        );

        $cat_relation = [];

        for($i = 0; $i < 25; $i++)
            $cat_relation[] = [
                'category_id' => rand(1,8),
                'product_id' => rand(1,11),
            ];

        DB::table("category_product")->insert($cat_relation);


    }
}
