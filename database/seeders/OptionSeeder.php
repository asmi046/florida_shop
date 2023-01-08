<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        php_uname();

        // if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

        DB::table("options")->insert(
            [
                [
                    "name" => "obmen",
                    'title' => 'Обмен возврат',
                    "value" => file_get_contents(public_path('texts/obmen.txt')),
                ],

                [
                    "name" => "delivery",
                    'title' => 'Доставка',
                    "value" => file_get_contents(public_path('texts/delivery.txt')),
                ],

                [
                    "name" => "policy",
                    'title' => 'Политика конфиденциальности',
                    "value" => file_get_contents(public_path('texts/policy.txt')),
                ],

                [
                    "name" => "adress",
                    'title' => 'Адрес',
                    "value" => "Карла Маркса, 72 корпус 18",
                ],

                [
                    "name" => "phone",
                    'title' => 'Телефон',
                    "value" => "+7 (4712) 545 545",
                ],

                [
                    "name" => "email",
                    'title' => 'e-mail',
                    "value" => "info@florida46.ru",
                ],

                [
                    "name" => "email_send",
                    'title' => 'e-mail для отправки',
                    "value" => "info@florida46.ru, asmi046@gmail.com",
                ],

                [
                    "name" => "main_h1",
                    'title' => 'Заголовок главной страницы',
                    "value" => "Доставка цветов в Курске на официальном сайте «Флорида»!",
                ],


                [
                    "name" => "main_text",
                    'title' => 'Текст на главной',
                    "value" => file_get_contents(public_path('texts/main_text.txt')),
                ],

                [
                    "name" => "telegram_lnk",
                    'title' => 'Ссылка Telegram',
                    "value" => "#",
                ],

                [
                    "name" => "whatsapp_lnk",
                    'title' => 'Ссылка WhatsApp',
                    "value" => "#",
                ],

                [
                    "name" => "vk_lnk",
                    'title' => 'Ссылка Vk',
                    "value" => "#",
                ],
            ]);
    }
}
