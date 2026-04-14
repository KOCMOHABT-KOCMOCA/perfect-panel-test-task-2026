<?php

use App\Exceptions\ApiException;
use App\Http\Responses\ApiErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            $shouldReturnJson = $request->expectsJson() || $request->is('api/*');

            if ($shouldReturnJson === false) {
                return null;
            }

            if ($e instanceof ApiException) {
                return new ApiErrorResponse($e->getMessage(), $e->getCode());
            }

            // todo 999 исправить код на 422 после исправления тестов
            if ($e instanceof ValidationException) {
                $firstError = collect($e->errors())->flatten()->first();
                return new ApiErrorResponse($firstError ?? 'Validation failed', 400);
            }

            $message = config('app.debug') ? $e->getMessage() : 'Server internal error';

            $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            return new ApiErrorResponse($message, $code);
        });
    })->create();
