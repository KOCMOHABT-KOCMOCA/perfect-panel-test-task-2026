<?php

declare(strict_types=1);

namespace App\Actions\Currency;

use App\DTO\Currency\ConvertRequest;
use App\Services\Currency\CurrencyService;

class ConvertAction
{
    public function __construct(private readonly CurrencyService $service) {}

    public function execute(ConvertRequest $request): array
    {
        return $this->service->convert(
            $request->currencyFrom,
            $request->currencyTo,
            $request->value
        );
    }
}
