<?php

namespace App\Actions;

use App\Http\Requests\BascetForm;

class OneClickToTextAction {
    public function handle(BascetForm $request, $zakaz_id="") {
        $rez_text = "<b>Оформлен заказ в 1 клик</b>\n\r";
        $rez_text .= "<b>".$zakaz_id."</b>\n\r";

        $rez_text .= "<strong>Имя:</strong> ".$request->input("fio")."\n\r";
        $rez_text .= "<strong>Телефон:</strong> ".$request->input("phone")."\n\r";


        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach ($request->input('tovars') as $item) {

            $rez_text .= $item["tovar_data"]["title"]." (Артикул:".$request->input("product_sku").")"."\n\r";
            $rez_text .= $item["tovar_data"]["price"]." ₽\n\r";
            $rez_text .= "Кол-во: 1\n\r";
            $rez_text .= "Подитог: " . (float)$item["tovar_data"]["price"]. "\n\r";

        }
        return $rez_text;
    }
}
