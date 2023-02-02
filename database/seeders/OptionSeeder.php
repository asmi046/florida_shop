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
                    "type" => "rich",
                    'title' => 'Обмен возврат',
                    "value" => file_get_contents(public_path('texts/obmen.txt')),
                ],

                [
                    "name" => "delivery",
                    "type" => "rich",
                    'title' => 'Доставка',
                    "value" => file_get_contents(public_path('texts/delivery.txt')),
                ],

                [
                    "name" => "policy",
                    "type" => "rich",
                    'title' => 'Политика конфиденциальности',
                    "value" => file_get_contents(public_path('texts/policy.txt')),
                ],

                [
                    "name" => "adress",
                    "type" => "plan",
                    'title' => 'Адрес',
                    "value" => "Карла Маркса, 72 корпус 18",
                ],

                [
                    "name" => "phone",
                    "type" => "plan",
                    'title' => 'Телефон',
                    "value" => "+7 (920) 710 25 55",
                ],

                [
                    "name" => "email",
                    "type" => "plan",
                    'title' => 'e-mail',
                    "value" => "info@florida46.ru",
                ],

                [
                    "name" => "email_send",
                    "type" => "plan",
                    'title' => 'e-mail для отправки',
                    "value" => "info@florida46.ru, asmi046@gmail.com",
                ],

                [
                    "name" => "main_h1",
                    "type" => "plan",
                    'title' => 'Заголовок главной страницы',
                    "value" => "Доставка цветов в Курске на официальном сайте «Флорида»!",
                ],


                [
                    "name" => "main_text",
                    "type" => "rich",
                    'title' => 'Текст на главной',
                    "value" => file_get_contents(public_path('texts/main_text.txt')),
                ],

                [
                    "name" => "telegram_lnk",
                    "type" => "plan",
                    'title' => 'Ссылка Telegram',
                    "value" => "tg://resolve?domain=floridasfl",
                ],

                [
                    "name" => "whatsapp_lnk",
                    "type" => "plan",
                    'title' => 'Ссылка WhatsApp',
                    "value" => "https://wa.me/792071025551",
                ],

                [
                    "name" => "vk_lnk",
                    "type" => "plan",
                    'title' => 'Ссылка Vk',
                    "value" => "https://vk.com/florida46kursk",
                ],
            ]);
    }
}
