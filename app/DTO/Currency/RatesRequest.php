<?php

declare(strict_types=1);

namespace App\DTO\Currency;

use Illuminate\Http\Request;

class RatesRequest
{
    public function __construct(
        public readonly ?array $currencies = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        $currencyParam = $request->query('currency');
        $currencies = $currencyParam ? explode(',', strtoupper($currencyParam)) : null;

        return new self($currencies);
    }
}
