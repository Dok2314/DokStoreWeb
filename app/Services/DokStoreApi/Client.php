<?php

namespace App\Services\DokStoreApi;

use App\Services\DokStoreApi\Operations\BaseOperation;
use App\Services\DokStoreApi\Operations\GetCarsOperation;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Log;

class Client
{
    protected mixed $clientId;
    protected mixed $clientSecret;
    protected mixed $baseApiUrl;

    protected array $history = [];

    public function __construct(array $config)
    {
        $this->baseApiUrl = $config['baseUrl'];
        $this->clientId = $config['clientId'];
        $this->clientSecret = $config['clientSecret'];
    }

    public function getCars()
    {
        $operation = new GetCarsOperation;
        return $this->send($operation)->getData();
    }

    protected function send(BaseOperation $operation)
    {
        $request = new ApiRequest($operation);
        $history = Middleware::history($this->history);
        $stack = HandlerStack::create();
        $stack->push($history);
        $method = $request->getMethod();
        $url = $request->getUrl();

        $options = ['handler' => $stack];

//        if ($operation->withCookie())
//            $options['cookies'] = $this->getCookies() ?? true;

        $transport = new \GuzzleHttp\Client($options);
        $typeData = strcasecmp('POST', $method) === 0 ? 'form_params' : 'query';

        $data = [
            $typeData => $request->getData(),
            'headers' => $this->getHeaders()
        ];

//        if (key_exists('multipart', $data[$typeData])) {
//            $data['multipart'] = $data[$typeData]['multipart'];
//            unset($data[$typeData]);
//        }

//        if (!key_exists('locale', $data[$typeData]))
//            $data[$typeData]['locale'] = app()->getLocale();


        try {
            $result = $transport->request($method, $this->getFullUrl($url), $data);
        } catch (ClientException $e) {
            Log::error('Send request api error: ' . $e->getMessage());
            $error = new Error($e);
            return $error->handleException();
        }

        if ($operation->withCookie())
            $this->saveCookies($transport->getConfig('cookies'));

        return new ApiResponse($result);
    }

    protected function getCookies()
    {
        $cookie = session('api_cookies', false);
        if ($cookie) {
            $jar = new \GuzzleHttp\Cookie\CookieJar;
            $domain = parse_url($this->baseApiUrl, PHP_URL_HOST);
            $cookieJar = $jar->fromArray(session('api_cookies'), $domain);
            return $cookieJar;
        }
    }

    protected function getHeaders()
    {
        $bearerToken = request()->get('apiToken');
        $headers = [
            'User-Agent' => request()->headers->get('User-Agent'),
            'Accept' => 'application/json',
            'Accept-Language' => app()->getLocale(),
            'X-Client-Ip' => request()->headers->get('X-Forwarded-For')?:request()->header('cf-connecting-ip') ?: request()->getClientIp()
        ];

        if (request()->headers->has('Timezone')) {
            $headers['Timezone'] = request()->headers->get('Timezone');
        }
        if ($bearerToken) {
            $headers['Authorization'] = 'Bearer ' . $bearerToken;
        }
        if (request()->headers->has('cf-iplongitude')) {
            $headers['cf-iplongitude'] = request()->header('cf-iplongitude');
        }
        if (request()->headers->has('cf-iplatitude')) {
            $headers['cf-iplatitude'] = request()->header('cf-iplatitude');
        }
        if (request()->headers->has('cf-ipcountry')) {
            $headers['cf-ipcountry'] = request()->header('cf-ipcountry');
        }
        if (request()->headers->has('cf-ipcity')) {
            $headers['cf-ipcity'] = request()->header('cf-ipcity');
            // Alternative to cf-ipcity, as cf-ipcity is not readable on the API server
            $headers['ipcity'] = request()->header('cf-ipcity');
        }
        if (request()->headers->has('system')) {
            $headers['system'] = request()->header('system');
        }
        if (request()->headers->has('cf-ipcontinent')) {
            $headers['cf-ipcontinent'] = request()->header('cf-ipcontinent');
        }

        return $headers;
    }

    protected function getFullUrl($url): string
    {
        return rtrim($this->baseApiUrl, '/') . '/' . ltrim($url, '/');
    }

    protected function saveCookies($cookieJar): void
    {
        $save = [];
        foreach ($cookieJar->toArray() as $cookie) {
            $save[$cookie['Name']] = $cookie['Value'];
        }
        session(['api_cookies' => $save]);
    }
}
