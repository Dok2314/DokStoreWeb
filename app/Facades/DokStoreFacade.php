<?php

namespace App\Facades;

use App\Services\DokStoreApi\Client;
use Illuminate\Support\Facades\Facade;

class DokStoreFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Client::class;
    }
}
