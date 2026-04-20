<?php

declare(strict_types=1);

namespace App\Services\Currency\Providers;

use App\DTO\Currency\ProviderRateDTO;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * @todo добавить другой fallback сервис вместо неработающего
 * @deprecated На момент написания API сервис coincap отключил упомянутый в задаче сервис получения валют
 */
class CoinCapProvider implements CurrencyProviderInterface
{
    /**
     * @return ProviderRateDTO
     * @throws ConnectionException
     */
    public function getRawRates(): ProviderRateDTO
    {
        $response = Http::timeout(3)->get(config('currency.coincap_url'));
        if (!$response->successful()) {
            throw new \Exception('CoinCap request failed');
        }

        $data = $response->json() ?? [];

        if (isset($data['coinValue']) === false) {
            //todo отделить логи ошибок провайдеров от обычных логов
            \Log::error('CoinCap returns unsupported result: ' . json_encode($data));
            throw new \Exception('CoinCap returns unsupported result');
        }

        return new ProviderRateDTO($data['coinValue']);
    }
}

