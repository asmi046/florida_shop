<?php

namespace App\Actions;

use App\Models\Product;
use App\Http\Requests\BascetForm;

class NalToMediaAction {
    public function handle(Product $product) {
        $media = [];

           $element = [
                'type' => 'photo',
                // 'media' => "https://florida46.ru/storage/2024/10/03/5123bb29b1aad296d1d474c060c9a37fec4d1b00.jpg"
                'media' => config('app.url').$product->img
            ];

        $media[] = $element;


        return $media;
    }
}
