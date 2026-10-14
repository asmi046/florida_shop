<?php

namespace App\Listeners;

use App\Services\AmoApiSevice;
use App\Events\ConsultationRequested;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConsultationRequestedAmo implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(ConsultationRequested $event): void
    {
        $amo = new AmoApiSevice();
        $amo->create_consult_lead(
            $event->name,
            $event->phone,
            $event->comment,
            $event->product
        );
    }
}
