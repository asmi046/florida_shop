<?php

namespace App\Actions;

use App\Http\Requests\BascetForm;

class BascetToMediaAction {
    public function handle(BascetForm $request, $zakaz_id) {
        $media = [];

        foreach ($request->input('tovars') as $item) {
            $element = [
                'type' => 'photo',
                // 'media' => "https://florida46.ru/storage/2024/10/03/5123bb29b1aad296d1d474c060c9a37fec4d1b00.jpg"
                'media' => config('app.url').$item["tovar_data"]["img"]
            ];

            $media[] = $element;
        }


        return $media;
    }
}
