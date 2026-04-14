<?php

use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Middleware\AuthenticateToken;
use Illuminate\Support\Facades\Route;

Route::middleware([AuthenticateToken::class])
    ->prefix('v1')
    ->group(function () {
        Route::match(['GET', 'POST'], '/', CurrencyController::class);
    });
