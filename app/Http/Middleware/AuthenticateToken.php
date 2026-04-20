<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Http\Responses\ApiErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token === null) {
            throw new ApiException('Unauthorized', 401);
        }

        if ($token !== config('currency.token')) {
            throw new ApiException('Invalid authentication token', 403);
        }

        return $next($request);
    }
}
