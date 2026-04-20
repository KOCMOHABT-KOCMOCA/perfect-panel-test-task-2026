<?php

declare(strict_types=1);

namespace App\DTO\Currency;

readonly class ProviderRateDTO
{
    public function __construct(public array $rates) {}
}
