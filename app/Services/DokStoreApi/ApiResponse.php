<?php

namespace App\Services\DokStoreApi;

use \GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    private GuzzleResponse $origResponse;

    public function __construct(GuzzleResponse $origResponse)
    {
        $this->origResponse = $origResponse;
    }

    public function getData()
    {
        try {
            return \GuzzleHttp\Utils::jsonDecode($this->origResponse->getBody()->__toString(), true);
        } catch (\Exception $e) {
            Log::critical(__METHOD__, [
                'response' => $this->origResponse->getBody()->__toString(),
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getStatusCode()
    {
        return $this->origResponse->getStatusCode();
    }
}
