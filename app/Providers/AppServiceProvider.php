<?php

namespace App\Providers;

use App\Services\GeminiService;
use App\Services\ReservationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ReservationService::class, function ($app) {
            return new ReservationService();
        });
        $this->app->singleton(GeminiService::class, function ($app) {
            return new GeminiService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
