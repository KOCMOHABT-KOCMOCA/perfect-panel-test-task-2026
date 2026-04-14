<?php

declare(strict_types=1);

return [
    'token' => env('CURRENCY_TOKEN'),
    'commission' => (float) env('CURRENCY_COMMISSION', 0.02),
    'coincap_url' => env('COINCAP_API_URL'),
    'coingate_url' => env('COINGATE_API_URL'),
    'base_currency' => env('CURRENCY_BASE', 'USD'),
];
