<?php

namespace App\Services\DokStoreApi;

use App\Exceptions\CustomExceptions\InvoiceHoldException;
use App\Exceptions\CustomExceptions\InvoiceProcessException;
use App\Exceptions\CustomExceptions\ThrottleException;
use App\Exceptions\CustomExceptions\UserBannedException;
use Illuminate\Support\Arr;
use App\Exceptions\CustomExceptions\CountryIsLockedForBuyException;
use App\Exceptions\CustomExceptions\CountryIsLockedForSellException;
use App\Exceptions\CustomExceptions\CurrencyNotImplementedException;
use App\Exceptions\CustomExceptions\EmailConfirmException;
use App\Exceptions\CustomExceptions\EmailNotSetException;
use App\Exceptions\CustomExceptions\InternalApiException;
use App\Exceptions\CustomExceptions\LoginException;
use App\Exceptions\CustomExceptions\NeedConfirmPhoneException;
use App\Exceptions\CustomExceptions\NeedGoogleOtpException;
use App\Exceptions\CustomExceptions\NeedVerificationException;
use App\Exceptions\CustomExceptions\NoExchangeRateException;
use App\Exceptions\CustomExceptions\NotFoundFiatExchangeRateException;
use App\Exceptions\CustomExceptions\PaymentApiException;
use App\Exceptions\CustomExceptions\TransactionAlreadyConfirmedException;
use App\Exceptions\CustomExceptions\TransactionIsCheckedException;
use App\Exceptions\CustomExceptions\ValidationException;
use App\Exceptions\CustomExceptions\NotTrustedIp;
use App\Exceptions\CustomExceptions\NoActiveProviderForCurrencyException;
use App\Exceptions\CustomExceptions\ConfirmEmailException;
use App\Exceptions\CustomExceptions\WalletOutageException;
use App\Exceptions\CustomExceptions\WalletIsDisabled;
use App\Exceptions\CustomExceptions\InvoiceReplenishException;
use App\Exceptions\CustomExceptions\ExtraFieldException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

/**
 * Class Error
 * @package App\Lib\HotcoinApi
 */
class Error
{
    protected $exception;
    protected $data;

    public function __construct(ClientException $exception)
    {
        $this->exception = $exception;
        $json = $this->exception->getResponse()->getBody()->getContents();
        try {
            $this->data = \GuzzleHttp\Utils::jsonDecode($json, true);
        } catch (\Exception $e) {
            Log::error('JSON parse error: ' . $e->getMessage());
            $this->data = [];
        }
    }

    public function handleException()
    {
        throw new \Exception();
    }

    public function getData()
    {
        return $this->data;
    }

    public function getCode()
    {
        return $this->exception->getCode();
    }

    public function getResultCode()
    {
        return Arr::get($this->data, 'code');
    }

    public function getStatus()
    {
        return Arr::get($this->data, 'status');
    }

    public function getMessage()
    {
        return Arr::get($this->data, 'error');
    }

    public function getDataMessage()
    {
        return Arr::get($this->data, 'message');
    }

    protected function getResultErrors()
    {
        return Arr::get($this->data, 'errors', []);
    }
}
