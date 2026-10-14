<?php

namespace App\Listeners;

use App\Events\ConsultationRequested;
use App\Actions\NalToMediaAction;
use App\Actions\TelegramSendAction;
use App\Actions\TelegramSendMediaAction;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConsultationRequestedTg implements ShouldQueue
{
    public function __construct() {}

    public function handle(ConsultationRequested $event): void
    {
        $tgsender = new TelegramSendAction();
        $tg_media = new TelegramSendMediaAction();
        $to_media = new NalToMediaAction();

        $name = $event->name;
        $phone = $event->phone;
        $review = $event->comment;

        if ($event->product) {
            $product = $event->product;
            $lnk = "<a href='".route('tovar', $product->slug)."'>".$product->title."</a>";

            $tgsender->handle(
                "<b>Запрос наличия букета</b>\n\rИмя: ".$name."\n\rТелефон: ".$phone."\n\rБукет:\n\r".$lnk
            );

            $media = $to_media->handle($product);
            $tg_media->handle($media);
        } else {
            $tgsender->handle(
                "<b>Консультация флориста</b>\n\rИмя: ".$name."\n\rТелефон: ".$phone."\n\rКомментарий: ".$review
            );
        }
    }
}
