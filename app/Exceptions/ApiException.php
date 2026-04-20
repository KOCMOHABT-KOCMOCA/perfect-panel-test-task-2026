<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Responses\ApiErrorResponse;
use Illuminate\Http\Request;
use Exception;

class ApiException extends Exception
{
    public function __construct(string $message = "", int $code = 400) {
        parent::__construct($message, $code);
    }

    /**
     * @param Request $request
     * @return ApiErrorResponse
     */
    public function render(Request $request): ApiErrorResponse
    {
        return new ApiErrorResponse(
            $this->getMessage(),
            $this->getCode() ?: 400
        );
    }
}
