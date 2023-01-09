<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Storage::disk('public')->put("boket_1.jpg", file_get_contents(public_path('img/facer_img/tovars/tov_2.jpg')), 'public');
        Storage::disk('public')->put("boket_2.jpg", file_get_contents(public_path('img/facer_img/tovars/tov_3.jpg')), 'public');
        Storage::disk('public')->put("boket_3.jpg", file_get_contents(public_path('img/facer_img/tovars/tov_4.jpg')), 'public');
        Storage::disk('public')->put("boket_4.jpg", file_get_contents(public_path('img/facer_img/tovars/tov_5.jpg')), 'public');
        Storage::disk('public')->put("boket_5.jpg", file_get_contents(public_path('img/facer_img/tovars/tov_6.jpg')), 'public');
        Storage::disk('public')->put("boket_6.jpg", file_get_contents(public_path('img/facer_img/tovars/tov_7.jpg')), 'public');

        DB::table("products")->insert(
            [
                [
                    'sku' => 'boket_1',
                    'title' => "Букет из розовых гипсофил",
                    'slug' => Str::slug("Букет из розовых гипсофил"),
                    'img' => Storage::url("boket_1.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гипсофила</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                        "<li>Топер</li>".
                    "</ul>",

                    'price' => 2500,
                    'old_price' => 0,
                    'hit' => 1,
                    'new' => 0,
                    'category' => "",
                    'height' => '50 см',
                    'radius' => '60 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет из розовых гипсофил",
                    'seo_description' => "Букет из розовых гипсофил в Курске"
                ],

                [
                    'sku' => 'boket_2',
                    'title' => "Букет из Герберы",
                    'slug' => Str::slug("Букет из Герберы"),
                    'img' => Storage::url("boket_1.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Розы кустовые</li>".
                        "<li>Чико</li>".
                        "<li>Альстромерия</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 3500,
                    'old_price' => 3800,
                    'hit' => 1,
                    'new' => 1,
                    'category' => "",
                    'height' => '45 см',
                    'radius' => '45 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет из Герберы",
                    'seo_description' => "Букет из Герберы в Курске"
                ],

                [
                    'sku' => 'boket_3',
                    'title' => "Букет Герберы и Розы",
                    'slug' => Str::slug("Букет Герберы и Розы"),
                    'img' => Storage::url("boket_3.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Сантини</li>".
                        "<li>Розы кустовые</li>".
                        "<li>Альстромерия</li>".
                        "<li>Эвкалипт</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 4500,
                    'old_price' => 0,
                    'hit' => 0,
                    'new' => 1,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '45 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Герберы и Розы",
                    'seo_description' => "Букет Герберы и Розы в Курске"
                ],

                [
                    'sku' => 'boket_4',
                    'title' => "Букет Герберы, Розы Сантини",
                    'slug' => Str::slug("Букет Герберы, Розы Сантини"),
                    'img' => Storage::url("boket_4.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Сантини</li>".
                        "<li>Розы</li>".
                        "<li>Альстромерия</li>".
                        "<li>Эвкалипт</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 2500,
                    'old_price' => 3800,
                    'hit' => 1,
                    'new' => 1,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '45 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Герберы, Розы Сантини",
                    'seo_description' => "Букет Герберы, Розы Сантини в Курске"
                ],

                [
                    'sku' => 'boket_5',
                    'title' => "Букет Герберы, Хризантемы",
                    'slug' => Str::slug("Букет Герберы, Хризантемы"),
                    'img' => Storage::url("boket_5.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Хризантема куст</li>".
                        "<li>Аспидистра</li>".
                        "<li>Эвкалипт</li>".
                        "<li>Фисташка</li>".
                        "<li>Топер</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 3500,
                    'old_price' => 3800,
                    'hit' => 1,
                    'new' => 0,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '40 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Герберы, Хризантемы",
                    'seo_description' => "Букет Герберы, Хризантемы в Курске"
                ],

                [
                    'sku' => 'boket_6',
                    'title' => "Букет Эквадорская Роза",
                    'slug' => Str::slug("Букет Эквадорская Роза"),
                    'img' => Storage::url("boket_6.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Эквадорская Роза</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 5500,
                    'old_price' => 6800,
                    'hit' => 1,
                    'new' => 1,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '80 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Эквадорская Роза",
                    'seo_description' => "Букет Эквадорская Роза в Курске"
                ],

                [
                    'sku' => 'boket_7',
                    'title' => "Букет Герберы, Розы Сантини #7",
                    'slug' => Str::slug("Букет Герберы, Розы Сантини #7"),
                    'img' => Storage::url("boket_4.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Сантини</li>".
                        "<li>Розы</li>".
                        "<li>Альстромерия</li>".
                        "<li>Эвкалипт</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 2500,
                    'old_price' => 0,
                    'hit' => 1,
                    'new' => 1,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '45 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Герберы, Розы Сантини",
                    'seo_description' => "Букет Герберы, Розы Сантини в Курске"
                ],

                [
                    'sku' => 'boket_8',
                    'title' => "Букет Герберы, Хризантемы #8",
                    'slug' => Str::slug("Букет Герберы, Хризантемы #8"),
                    'img' => Storage::url("boket_5.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Хризантема куст</li>".
                        "<li>Аспидистра</li>".
                        "<li>Эвкалипт</li>".
                        "<li>Фисташка</li>".
                        "<li>Топер</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 3500,
                    'old_price' => 3800,
                    'hit' => 1,
                    'new' => 0,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '40 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Герберы, Хризантемы",
                    'seo_description' => "Букет Герберы, Хризантемы в Курске"
                ],

                [
                    'sku' => 'boket_9',
                    'title' => "Букет Эквадорская Роза #9",
                    'slug' => Str::slug("Букет Эквадорская Роза #9"),
                    'img' => Storage::url("boket_6.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Эквадорская Роза</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 5500,
                    'old_price' => 6800,
                    'hit' => 1,
                    'new' => 1,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '80 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Эквадорская Роза",
                    'seo_description' => "Букет Эквадорская Роза в Курске"
                ],

                [
                    'sku' => 'boket_10',
                    'title' => "Букет Герберы, Хризантемы #10",
                    'slug' => Str::slug("Букет Герберы, Хризантемы #10"),
                    'img' => Storage::url("boket_5.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Гербера</li>".
                        "<li>Хризантема куст</li>".
                        "<li>Аспидистра</li>".
                        "<li>Эвкалипт</li>".
                        "<li>Фисташка</li>".
                        "<li>Топер</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 3500,
                    'old_price' => 0,
                    'hit' => 1,
                    'new' => 0,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '40 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Герберы, Хризантемы",
                    'seo_description' => "Букет Герберы, Хризантемы в Курске"
                ],

                [
                    'sku' => 'boket_11',
                    'title' => "Букет Эквадорская Роза #11",
                    'slug' => Str::slug("Букет Эквадорская Роза #11"),
                    'img' => Storage::url("boket_6.jpg"),
                    'description' =>
                    "<ul>".
                        "<li>Эквадорская Роза</li>".
                        "<li>Упаковка (пленка корейская)</li>".
                    "</ul>",

                    'price' => 5500,
                    'old_price' => 0,
                    'hit' => 1,
                    'new' => 1,
                    'category' => "",
                    'height' => '55 см',
                    'radius' => '80 см',
                    'sales_count' => rand(0, 45),
                    'seo_title' => "Букет Эквадорская Роза",
                    'seo_description' => "Букет Эквадорская Роза в Курске"
                ],
            ]);

            $images = [];

            for ($i=1; $i<11; $i++) {
                $images[] = [
                    'product_id' => $i,
                    'link' => Storage::url("boket_".rand(1, 6).".jpg"),
                    'alt' => "Фото товара",
                    'title' => "Фото товара"
                ];

                $images[] = [
                    'product_id' => $i,
                    'link' => Storage::url("boket_".rand(1, 6).".jpg"),
                    'alt' => "Фото товара",
                    'title' => "Фото товара"
                ];

                $images[] = [
                    'product_id' => $i,
                    'link' => Storage::url("boket_".rand(1, 6).".jpg"),
                    'alt' => "Фото товара",
                    'title' => "Фото товара"
                ];

                $images[] = [
                    'product_id' => $i,
                    'link' => Storage::url("boket_".rand(1, 6).".jpg"),
                    'alt' => "Фото товара",
                    'title' => "Фото товара"
                ];

                $images[] = [
                    'product_id' => $i,
                    'link' => Storage::url("boket_".rand(1, 6).".jpg"),
                    'alt' => "Фото товара",
                    'title' => "Фото товара"
                ];
            }

            DB::table("product_images")->insert($images);
    }
}
