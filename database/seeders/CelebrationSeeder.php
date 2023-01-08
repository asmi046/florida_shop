<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use DB;

class CelebrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("celebrations")->insert(
            [
                [ 'title' => "23 февраля", 'slug' => Str::slug("23 февраля") ],
                [ 'title' => "День победы", 'slug' => Str::slug("День победы") ],
                [ 'title' => "День рождения", 'slug' => Str::slug("День рождения") ],
                [ 'title' => "Татьянин день", 'slug' => Str::slug("Татьянин день") ],
                [ 'title' => "День семьи", 'slug' => Str::slug("День семьи") ],
                [ 'title' => "Новый год", 'slug' => Str::slug("Новый год") ],
                [ 'title' => "14 февраля", 'slug' => Str::slug("14 февраля") ],
                [ 'title' => "День матери", 'slug' => Str::slug("День матери") ],
                [ 'title' => "8 марта", 'slug' => Str::slug("8 марта") ],
                [ 'title' => "1 сентября", 'slug' => Str::slug("1 сентября") ],
                [ 'title' => "Выпускной", 'slug' => Str::slug("Выпускной") ]
            ]);
    }
}
