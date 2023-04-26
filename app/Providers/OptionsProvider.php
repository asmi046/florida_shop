<?php

namespace App\Providers;

use App\Models\Option;
use App\Models\Category;
use App\Models\Celebration;
use Illuminate\Support\ServiceProvider;

use View;

class OptionsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $all_options = Option::all();
            $categoryes = Category::all();
            $celebrations = Celebration::all();

            $opt = [];

            foreach ($all_options as $otion) {
                $opt[$otion['name']] = $otion['value'];
            }
            View::share('options', $opt);
            View::share('all_cat', $categoryes);
            View::share('celebrations', $celebrations);
        });
    }
}
