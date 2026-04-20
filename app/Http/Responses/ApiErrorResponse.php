<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ApiErrorResponse implements Responsable
{
    public function __construct(
        private string $message,
        private int $code = 400,
        private string $status = 'error'
    ) {}

    /**
     * @param $request
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status'  => $this->status,
            'code'    => $this->code,
            'message' => $this->message,
        ], $this->code);
    }
}
