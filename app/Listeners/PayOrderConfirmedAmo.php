<?php

namespace App\Listeners;

use App\Services\AmoApiSevice;
use App\Events\PayOrderConfirmed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayOrderConfirmedAmo implements ShouldQueue
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
        $amo = new AmoApiSevice();
        $amo->create_order($event->request, $event->order_number);
    }
}
