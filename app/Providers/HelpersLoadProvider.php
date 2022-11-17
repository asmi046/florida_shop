<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelpersLoadProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $file = app_path('helpers/allHelpers.php'); 
        if (file_exists($file)) { 
            require_once($file); 
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
