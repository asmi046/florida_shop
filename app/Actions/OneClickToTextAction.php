<?php

namespace App\Actions;

use App\Http\Requests\BascetForm;

class OneClickToTextAction {
    public function handle(BascetForm $request) {
        $rez_text = "<b>Оформлен заказ в 1 клик</b>\n\r";

        $rez_text .= "<strong>Имя:</strong> ".$request->input("fio")."\n\r";
        $rez_text .= "<strong>Телефон:</strong> ".$request->input("phone")."\n\r";
        // $rez_text .= "<strong>E-mail:</strong> ".$request->input("email")."\n\r";
        // $rez_text .= "\n\r\n\r<b>Получатель</b>\n\r\n\r";
        // $rez_text .= "<strong>Имя получателя:</strong> ".$request->input("polname")."\n\r";
        // $rez_text .= "<strong>Телефон получателя:</strong> ".$request->input("polphone")."\n\r";

        // $rez_text .= "\n\r\n\r<b>Адрес доставки</b>\n\r\n\r";
        // $rez_text .= "<strong>Адрес:</strong> ".$request->input("delivery")."\n\r";
        // $rez_text .= "<strong>Подъезд:</strong> ".$request->input("podezd")."\n\r";
        // $rez_text .= "<strong>Этаж:</strong> ".$request->input("etazg")."\n\r";
        // $rez_text .= "<strong>Квартира:</strong> ".$request->input("kvartira")."\n\r";
        // $rez_text .= "<strong>Комментарий:</strong> ".$request->input("comment")."\n\r";

        // $rez_text .= "\n\r\n\r<b>Время доставки</b>\n\r\n\r";
        // $rez_text .= "<strong>Дата доставки:</strong> ".$request->input("data")."\n\r";
        // $rez_text .= "<strong>Время доставки:</strong> ".$request->input("time")."\n\r";

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
