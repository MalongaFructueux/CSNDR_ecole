<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Fix MySQL "Specified key was too long; max key length" errors on older MySQL versions
        // by limiting default string length for indexed VARCHAR columns under utf8mb4.
        Schema::defaultStringLength(191);
    }
}
