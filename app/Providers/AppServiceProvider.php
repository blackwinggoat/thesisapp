<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        setlocale(LC_ALL, "id");
        Carbon::setLocale("id");
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // For example im gonna locale all dates to Indonesian (ID)
        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');
    }
}
