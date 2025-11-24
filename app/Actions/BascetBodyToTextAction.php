<?php

namespace App\Actions;

use App\Models\Order;


class BascetBodyToTextAction {
    public function handle(Order $request, $lnk_text = true) {

        $rez_text = "Состав заказа\n\r\n\r";

        foreach ($request->items as $item) {

            $rez_text .= $item->title." (Артикул: ".$item->sku.")"."\n\r";
            if ($lnk_text)
                $rez_text .= "<a href='".route("tovar", $item->slug)."'>Посмотреть товар</a> \n\r";
            else
                $rez_text .= route("tovar", $item->slug)."\n\r";
            $rez_text .= $item->price." ₽\n\r";
            $rez_text .= "Кол-во: " . $item->quantity."\n\r";
            $rez_text .= "Подитог: " . (float)$item->total."\n\r";
            $rez_text .= "---------\n\r";
        }

        return $rez_text;
    }
}
