<?php

namespace App\Providers;
use App\Events\PayOrderConfirmed;
use App\Events\ConsultationRequested;

use Illuminate\Support\Facades\Event;
use App\Listeners\PayOrderConfirmedTg;
use Illuminate\Auth\Events\Registered;
use App\Listeners\PayOrderConfirmedAmo;
use App\Listeners\PayOrderConfirmedEmail;
use App\Listeners\ConsultationRequestedAmo;
use App\Listeners\ConsultationRequestedTg;
use App\Listeners\ConsultationRequestedEmail;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Ваше событие с одним слушателем
        PayOrderConfirmed::class => [
            PayOrderConfirmedEmail::class,
            PayOrderConfirmedAmo::class,
            PayOrderConfirmedTg::class
        ],

        ConsultationRequested::class => [
            ConsultationRequestedAmo::class,
            ConsultationRequestedTg::class,
            ConsultationRequestedEmail::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
