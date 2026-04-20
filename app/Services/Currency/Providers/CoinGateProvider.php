<?php

declare(strict_types=1);

namespace App\Services\Currency\Providers;

use App\DTO\Currency\ProviderRateDTO;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class CoinGateProvider implements CurrencyProviderInterface
{
    /**
     * @return ProviderRateDTO
     * @throws ConnectionException
     */
    public function getRawRates(): ProviderRateDTO
    {
        $response = Http::timeout(3)->get(config('currency.coingate_url'));
        if (!$response->successful()) {
            throw new \Exception('CoinGate request failed');
        }

        $data = $response->json() ?? [];

        if (isset($data['merchant']) === false) {
            //todo отделить логи ошибок провайдеров от обычных логов
            \Log::error('CoinGate returns unsupported result: ' . json_encode($data));
            throw new \Exception('CoinGate returns unsupported result');
        }

        return new ProviderRateDTO($data['merchant']);
    }
}
