<?php

namespace App\Providers;

use App\Services\DokStoreApi\Client;
use Illuminate\Support\ServiceProvider;

class DokStoreServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app){
            $config = config('dokstore');
            return new Client($config);
        });
    }
}
