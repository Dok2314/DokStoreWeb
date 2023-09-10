<?php

namespace App\Services\DokStoreApi\Operations;

class GetCarsOperation extends BaseOperation
{
    public function getUrl() :string
    {
        return '/api/cars';
    }

    public function getHttpMethod() :string
    {
        return 'GET';
    }
}
