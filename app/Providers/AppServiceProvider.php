<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Option;
use App\Models\Category;
use App\Models\Celebration;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(\Schema::hasTable('options')) {
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
        }
    }
}
