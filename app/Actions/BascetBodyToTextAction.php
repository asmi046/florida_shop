<?php

namespace App\Actions;

use App\Http\Requests\BascetForm;

class BascetBodyToTextAction {
    public function handle(BascetForm $request) {

        $rez_text = "Состав заказа\n\r\n\r";

        foreach ($request->input('tovars') as $item) {
            $rez_text .= $item["tovar_data"]["title"]." (Артикул:".$request->input("product_sku").")"."\n\r";
            $rez_text .= $item["tovar_data"]["price"]." ₽\n\r";
            $rez_text .= "Кол-во: " . $item["quentity"]."\n\r";
            $rez_text .= "Подитог: " . (float)$item["quentity"] * (float)$item["tovar_data"]["price"]."\n\r";
            $rez_text .= "---------\n\r";
        }

        return $rez_text;
    }
}
