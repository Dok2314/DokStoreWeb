<?php

namespace App\Services\DokStoreApi;

use App\Services\DokStoreApi\Operations\BaseOperation;

class ApiRequest
{
    protected BaseOperation $operation;

    public function __construct(BaseOperation $operation)
    {
        $this->operation = $operation;
    }

    public function getMethod(): string
    {
        return $this->operation->getHttpMethod();
    }

    public function getUrl(): string
    {
        return $this->operation->getUrl();
    }

    public function getData(): array
    {
        return $this->operation->getData();
    }
}
