<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                    'title' => "Сборные букеты", 'slug' => "",
                ],

                [
                    'title' => "Цветы в коробках", 'slug' => "",
                ],

                [
                    'title' => "Сборные букеты из Роз", 'slug' => "",
                ],

                [
                    'title' => "Свадебные букеты", 'slug' => "",
                ],

                [
                    'title' => "Монобукеты", 'slug' => "",
                ],

                [
                    'title' => "Необычьные", 'slug' => "",
                ],

                [
                    'title' => "Тюльпаны", 'slug' => "",
                ],

                [
                    'title' => "Подарки и игрушки", 'slug' => "",
                ],
            ]
        );
    }
}
