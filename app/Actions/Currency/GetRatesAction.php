<?php

declare(strict_types=1);

namespace App\Actions\Currency;

use App\DTO\Currency\RatesRequest;
use App\Services\Currency\CurrencyService;

class GetRatesAction
{
    public function __construct(private readonly CurrencyService $service) {}

    public function execute(RatesRequest $request): array
    {
        return $this->service->getRates($request->currencies);
    }
}
