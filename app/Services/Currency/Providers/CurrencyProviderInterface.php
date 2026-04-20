<?php

declare(strict_types=1);

namespace App\Services\Currency\Providers;

use App\DTO\Currency\ProviderRateDTO;

interface CurrencyProviderInterface
{
    /**
     * @return ProviderRateDTO
     */
    public function getRawRates(): ProviderRateDTO;
}
