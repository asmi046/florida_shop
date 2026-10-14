<?php

namespace App\Listeners;

use App\Mail\ConsultMail;
use App\Events\ConsultationRequested;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConsultationRequestedEmail implements ShouldQueue
{
    public function __construct() {}

    public function handle(ConsultationRequested $event): void
    {
        $formData = [
            'name' => $event->name,
            'phone' => $event->phone,
            'review' => $event->comment,
            'sku' => $event->product?->sku,
        ];

        Mail::to(explode(',', config('mailadresat.adresats')))
            ->send(new ConsultMail($formData));
    }
}
