<?php

namespace App\Services\DokStoreApi\Operations;

abstract class BaseOperation
{
    protected array $data = [];

    abstract public function getUrl() :string;

    public function getHttpMethod() :string
    {
        return 'POST';
    }

    public function getData() :array
    {
        return $this->data;
    }

    public function withCookie() :bool
    {
        return false;
    }
}
