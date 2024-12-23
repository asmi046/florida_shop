<?php

namespace App\Actions;

use App\Http\Requests\BascetForm;

class BascetToMediaAction {
    public function handle(BascetForm $request, $zakaz_id) {
        $media = [];

        foreach ($request->input('tovars') as $item) {
            $element = [
                'type' => 'photo',
                'media' => config('app.url').$item["tovar_data"]["img"]
            ];

            $media[] = $element;
        }


        return $media;
    }
}
