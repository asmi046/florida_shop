<?php

namespace App\Actions;

use App\Models\Order;

class BascetToTextAction {
    public function handle(Order $order, $zakaz_id) {
        $rez_text = "<b>Оформлен заказ</b>\n\r";
        $rez_text .= "<b>".$zakaz_id."</b>\n\r";

        if ($order->delivery_type) {
            $rez_text .= "<strong>Тип получения:</strong> ".$order->delivery_type."\n\r";
        }
        $rez_text .= "<strong>Имя:</strong> ".$order->fio."\n\r";
        $rez_text .= "<strong>Телефон:</strong> ".$order->phone."\n\r";
        $rez_text .= "<strong>E-mail:</strong> ".$order->email."\n\r";
        $rez_text .= "\n\r\n\r<b>Получатель</b>\n\r\n\r";
        $rez_text .= "<strong>Кто получатель:</strong> ".$order->recipient_type."\n\r";
        $rez_text .= "<strong>Имя получателя:</strong> ".$order->polname."\n\r";
        $rez_text .= "<strong>Телефон получателя:</strong> ".$order->polphone."\n\r";
        if ($order->anonymous_delivery) {
            $rez_text .= "<strong>Доставить анонимно</strong>\n\r";
        }

        // Адресный блок скрываем для самовывоза
        if ($order->delivery_type != 'Самовывоз') {
            $rez_text .= "\n\r\n\r<b>Адрес доставки</b>\n\r\n\r";
            $rez_text .= "<strong>Район:</strong> ".$order->raion."\n\r";
            $rez_text .= "<strong>Адрес:</strong> ".$order->delivery."\n\r";
            $rez_text .= "<strong>Подъезд:</strong> ".$order->podezd."\n\r";
            $rez_text .= "<strong>Этаж:</strong> ".$order->etazg."\n\r";
            $rez_text .= "<strong>Квартира:</strong> ".$order->kvartira."\n\r";
        }
        $rez_text .= "<strong>Комментарий:</strong> ".$order->comment."\n\r";

        $rez_text .= "\n\r\n\r<b>Время доставки</b>\n\r\n\r";
        $rez_text .= "<strong>Дата доставки:</strong> ". date('d.m.Y', strtotime($order->data))."\n\r";
        if ($order->deliver_asap) {
            $rez_text .= "<strong>Доставить в ближайшее время</strong>\n\r";
        } elseif ($order->agree_delivery_time_with_recipient) {
            $rez_text .= "<strong>Время согласовать с получателем</strong>\n\r";
        } else {
            $rez_text .= "<strong>Время доставки:</strong> ".$order->time."\n\r";
        }

        if ($order->confirm_address_with_recipient) {
            $rez_text .= "<strong>Адрес уточнить у получателя</strong>\n\r";
        }

        $rez_text .= "\n\r\n\r<b>Состав заказа</b>\n\r\n\r";

        foreach ($order->items as $item) {
            $rez_text .= $item->title." (Артикул: ".$item->sku.")"."\n\r";
            $rez_text .= "<a href='".route("tovar", $item->slug)."'>Посмотреть товар</a> \n\r";
            $rez_text .= $item->price." ₽\n\r";
            $rez_text .= "Кол-во: " . $item->quantity."\n\r";
            $rez_text .= "Подитог: " . (float)$item->total."\n\r";
            $rez_text .= "---------\n\r";
        }

        $rez_text .= "<strong>Цена доставки:</strong> ".$order->delivery_price." ₽\n\r";
        $rez_text .= "\n\r\n\r<b>Итого</b> " . $order->count . " товар(ов) на сумму ".$order->amount." ₽\n\r";

        return $rez_text;
    }
}
