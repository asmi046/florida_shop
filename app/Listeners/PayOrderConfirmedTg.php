<?php

namespace App\Listeners;

use App\Events\PayOrderConfirmed;
use App\Actions\BascetToTextAction;
use App\Actions\TelegramSendAction;
use App\Actions\BascetToMediaAction;
use App\Actions\TelegramSendMediaAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayOrderConfirmedTg implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PayOrderConfirmed $event): void
    {
        $to_text = new BascetToTextAction();
        $to_media = new BascetToMediaAction();
        $tgsender = new TelegramSendAction();
        $tg_media = new TelegramSendMediaAction();

        $to_text = $to_text->handle($event->request, $event->order_number);
        $media = $to_media->handle($event->request, $event->order_number);
        $tgsender->handle($to_text);
        $tg_media->handle($media);
    }
}
