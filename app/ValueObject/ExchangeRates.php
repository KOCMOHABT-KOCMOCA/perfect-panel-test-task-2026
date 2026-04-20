<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * Immutable value object for exchange rate matrix
 * currencyCode => [targetCurrencyCode => rate]
 */
final readonly class ExchangeRates
{
    /**
     * @param array<string, array<string, float>> $rates
     */
    private function __construct(private array $rates) {}

    /**
     * @param array $rates
     * @return self
     */
    public static function fromArray(array $rates): self
    {
        self::validateRatesMatrix($rates);

        return new self($rates);
    }

    /**
     * @param array $rates
     * @return void
     */
    private static function validateRatesMatrix(array $rates): void
    {
        foreach ($rates as $currency => $innerRates) {
            if (!is_string($currency) || !is_array($innerRates)) {
                throw new \InvalidArgumentException('Invalid exchange rates structure');
            }

            foreach ($innerRates as $target => $rate) {
                if (!is_string($target) || !is_float($rate)) {
                    throw new \InvalidArgumentException('Rate must be numeric');
                }
            }
        }
    }

    /**
     * @param float $commission
     * @return self
     */
    public function applyCommission(float $commission): self
    {
        if ($commission <= 0.0) {
            throw new \InvalidArgumentException('Commission must be positive');
        }

        $newRates = array_map(
            static fn (array $innerRates): array => array_map(
                static fn (float $rate): float => $rate * $commission,
                $innerRates
            ),
            $this->rates
        );

        return new self($newRates);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->rates;
    }
}
