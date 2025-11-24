<?php

namespace App\Listeners;

use App\Mail\BascetSend;
use App\Events\PayOrderConfirmed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        Mail::to(explode(",", config('mailadresat.adresats')))->send(new BascetSend($event->request));
    }
}
