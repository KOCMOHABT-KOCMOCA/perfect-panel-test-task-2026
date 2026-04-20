<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSuccessResponse implements Responsable
{
    public function __construct(
        private readonly mixed $data,
        private readonly int $code = 200
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code'   => $this->code,
            'data'   => $this->data,
        ], $this->code);
    }
}
