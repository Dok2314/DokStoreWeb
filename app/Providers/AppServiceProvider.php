<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
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
        $baseUrl = request()->getSchemeAndHttpHost();

        Http::macro('withDefaultOrigin', function () use ($baseUrl) {
            return Http::withHeaders([
                'Origin' => $baseUrl,
            ]);
        });
    }
}
