<?php

namespace App\Listeners;

use App\Events\PayOrderConfirmed;
use App\Mail\BascetSend;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayOrderConfirmedEmail implements ShouldQueue
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
        // Log::channel('pay')->error('Адреса уведомлений: '.config('mailadresat.adresats'));
        Mail::to(explode(',', config('mailadresat.adresats')))->send(new BascetSend($event->request));
    }
}
