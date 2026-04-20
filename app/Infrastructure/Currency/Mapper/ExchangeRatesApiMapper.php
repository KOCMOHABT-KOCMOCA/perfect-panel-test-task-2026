<?php

declare(strict_types=1);

namespace App\Infrastructure\Currency\Mapper;

use App\Exceptions\ApiException;
use App\ValueObject\ExchangeRates;

final readonly class ExchangeRatesApiMapper
{
    /**
     * @param array $flatApiData
     * @return ExchangeRates
     * @throws ApiException
     */
    public static function fromFlatApiResponse(array $flatApiData): ExchangeRates
    {
        if (empty($flatApiData)) {
            throw new \InvalidArgumentException('Empty rates from API');
        }

        $normalized = [];
        foreach ($flatApiData as $currency => $rawRate) {
            if (!is_string($currency)) {
                throw new \InvalidArgumentException("Currency must be string");
            }

            $normalized[$currency] = [];
            foreach ($rawRate as $to => $rate) {
                if (!is_string($to)) {
                    throw new \InvalidArgumentException("Target currency must be string");
                }

                $normalized[$currency][$to] = self::toFloat($rate);
            }
        }

        return ExchangeRates::fromArray($normalized);
    }

    /**
     * @param mixed $value
     * @return float
     * @throws ApiException
     */
    private static function toFloat(mixed $value): float
    {
        if (is_float($value) || is_int($value)) {
            return (float) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        throw new ApiException(
            sprintf('Invalid rate value: %s (expected numeric string, int or float)', get_debug_type($value))
        );
    }
}
