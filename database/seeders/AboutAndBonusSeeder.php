<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class AboutAndBonusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("options")->insert(
            [
                [
                    "name" => "bonus-system",
                    "type" => "rich",
                    'title' => 'Бонусная система',
                    "value" => file_get_contents(public_path('texts//bonus.html')),
                ],

                [
                    "name" => "about",
                    "type" => "rich",
                    'title' => 'О компании',
                    "value" => file_get_contents(public_path('texts//about.html')),
                ]
            ]
        );
    }
}
