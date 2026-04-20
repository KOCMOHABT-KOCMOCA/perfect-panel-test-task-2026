<?php

declare(strict_types=1);

namespace App\Services\Currency;

use App\DTO\Currency\ProviderRateDTO;
use App\Exceptions\ApiException;
use App\Infrastructure\Currency\Mapper\ExchangeRatesApiMapper;
use App\Services\Currency\Providers\CoinCapProvider;
use App\Services\Currency\Providers\CoinGateProvider;
use App\Services\Currency\Providers\CurrencyProviderInterface;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    /**
     * @var CurrencyProviderInterface[]
     */
    private array $providers;

    /**
     * @param CoinCapProvider $coinCapProvider
     * @param CoinGateProvider $coinGateProvider
     */
    public function __construct(CoinCapProvider $coinCapProvider, CoinGateProvider $coinGateProvider)
    {
        $this->providers = [
            $coinCapProvider,
            $coinGateProvider
        ];
    }

    /**
     * @param array<string>|null $filterCurrencies
     * @return array
     * @throws \JsonException
     */
    public function getRates(?array $filterCurrencies = null): array
    {
        //todo добавить теги кеша
        //todo добавить ручной сброс кеша
        //todo добавить автоматический сброс кеша при изменении курса и увеличить TTL
        $cacheKey = $this->getCurrencyRatesCacheKey($filterCurrencies);

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filterCurrencies) {
            $rates = ExchangeRatesApiMapper::fromFlatApiResponse(
                $this->fetchFromAnyProvider()->rates
            );

            $commission = 1 + config('currency.commission', 0.02);
            $ratesWithCommission = $rates->applyCommission($commission);
            $finalRates = $ratesWithCommission->toArray();

            if (empty($filterCurrencies) === false) {
                $finalRates = array_intersect_key($finalRates, array_flip($filterCurrencies));
            }

            asort($finalRates, SORT_NUMERIC);

            return $finalRates;
        });
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $value
     * @return array
     * @throws ApiException
     */
    public function convert(string $from, string $to, float $value): array
    {
        $rates = $this->getRates([$from]);

        if (isset($rates[$from]) === false) {
            throw new ApiException("Rates for {$from} not found");
        }

        if (isset($rates[$from][$to]) === false) {
            throw new ApiException("Rates for {$from}: {$to} not found");
        }

        $commission = (1 - config('currency.commission'));
        $amount = $value * $rates[$from][$to];
        $amountWithCommission = $amount * $commission;

        //todo fix to correct fiat/crypto validation
        if (strtoupper($to) === 'USD') {
            $converted = round($amountWithCommission, 2);
        } else {
            $converted = round($amountWithCommission, 10);
        }

        if ($value < 0.01) {
            throw new ApiException("Minimum amount is 0.01");
        }

        return [
            'currency_from' => strtoupper($from),
            'currency_to' => strtoupper($to),
            'value' => $value,
            'converted_value' => $converted,
            'rate' => round($rates[$from][$to] * $commission, 10),
        ];
    }

    /**
     * @return ProviderRateDTO
     * @throws ApiException
     */
    private function fetchFromAnyProvider(): ProviderRateDTO
    {
        foreach ($this->providers as $provider) {
            try {
                $data = $provider->getRawRates();
                if (empty($data) === false) {
                    return $data;
                }
            } catch (\Throwable $e) {
                \Log::warning("Provider " . get_class($provider) . " failed: " . $e->getMessage());
                continue;
            }
        }

        throw new ApiException('All currency providers are unavailable', 503);
    }

    /**
     * @param array|null $filterCurrencies
     * @return string
     * @throws \JsonException
     */
    private function getCurrencyRatesCacheKey(?array $filterCurrencies): string
    {
        if (empty($filterCurrencies) === true) {
            return 'currency_rates:all';
        }

        $normalized = array_unique(
            array_map('strtoupper', array_filter($filterCurrencies, 'is_string'))
        );
        sort($normalized);

        $hash = md5(json_encode($normalized, JSON_THROW_ON_ERROR));

        return "currency_rates:filtered:{$hash}";
    }
}
