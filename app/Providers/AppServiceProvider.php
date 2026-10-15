<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (($timezone = get_global_setting('timezone'))) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        View::composer('*', function ($view) {
            $view->with('global_config', (object) get_global_setting());
            $view->with('theme_config', (object) get_theme_config());
        });
    }
}
