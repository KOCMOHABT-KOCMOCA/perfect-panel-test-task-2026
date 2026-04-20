<?php

declare(strict_types=1);

namespace App\DTO\Currency;

use Illuminate\Http\Request;

class ConvertRequest
{
    public function __construct(
        public readonly string $currencyFrom,
        public readonly string $currencyTo,
        public readonly float $value
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            strtoupper($request->query('currency_from')),
            strtoupper($request->query('currency_to')),
            (float) $request->query('value'),
        );
    }
}
