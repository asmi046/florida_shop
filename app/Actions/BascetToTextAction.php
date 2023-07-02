<?php

namespace App\Actions;

use App\Http\Requests\BascetForm;

class BascetToTextAction {
    public function handle(BascetForm $request) {
        $rez_text = "<b>Оформлен заказ</b>\n\r";

        $rez_text .= "<strong>Имя:</strong> ".$request->input("fio")."\n\r";
        $rez_text .= "<strong>Телефон:</strong> ".$request->input("phone")."\n\r";
        $rez_text .= "<strong>E-mail:</strong> ".$request->input("email")."\n\r";
        $rez_text .= "\n\r\n\r<b>Получатель</b>\n\r\n\r";
        $rez_text .= "<strong>Имя получателя:</strong> ".$request->input("polname")."\n\r";
        $rez_text .= "<strong>Телефон получателя:</strong> ".$request->input("polphone")."\n\r";

        $rez_text .= "\n\r\n\r<b>Адрес доставки</b>\n\r\n\r";
        $rez_text .= "<strong>Адрес:</strong> ".$request->input("delivery")."\n\r";
        $rez_text .= "<strong>Подъезд:</strong> ".$request->input("podezd")."\n\r";
        $rez_text .= "<strong>Этаж:</strong> ".$request->input("etazg")."\n\r";
        $rez_text .= "<strong>Квартира:</strong> ".$request->input("kvartira")."\n\r";
        $rez_text .= "<strong>Комментарий:</strong> ".$request->input("comment")."\n\r";

        $rez_text .= "\n\r\n\r<b>Время доставки</b>\n\r\n\r";
        $rez_text .= "<strong>Дата доставки:</strong> ".$request->input("data")."\n\r";
        $rez_text .= "<strong>Время доставки:</strong> ".$request->input("time")."\n\r";

        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach ($request->input('tovars') as $item) {
            $rez_text .= $item["tovar_data"]["title"]." (Артикул:".$request->input("product_sku").")"."\n\r";
            $rez_text .= $item["tovar_data"]["price"]." ₽\n\r";
            $rez_text .= "Кол-во: " . $item["quentity"]."\n\r";
            $rez_text .= "Подитог: " . (float)$item["quentity"] * (float)$item["tovar_data"]["price"]."\n\r";
            $rez_text .= "---------\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Итого</b> " . $request->input("count") . " товар(ов) на сумму ".$request->input("amount");

        return $rez_text;
    }
}
