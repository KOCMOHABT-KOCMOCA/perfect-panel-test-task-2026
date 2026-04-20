<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Currency\ConvertAction;
use App\Actions\Currency\GetRatesAction;
use App\DTO\Currency\ConvertRequest;
use App\DTO\Currency\RatesRequest;
use App\Exceptions\ApiException;
use App\Http\Requests\Api\V1\CurrencyRequest;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly GetRatesAction $getRatesAction,
        private readonly ConvertAction $convertAction
    ) {}

    /**
     * @throws ApiException
     */
    public function __invoke(CurrencyRequest $request)
    {
        $method = $request->query('method');

        $data = match ($method) {
            'rates' => $this->getRatesAction->execute(RatesRequest::fromRequest($request)),
            'convert' => $this->convertAction->execute(ConvertRequest::fromRequest($request)),
            default => throw new ApiException("Unknown method {$method}"),
        };

        return new ApiSuccessResponse($data);
    }
}
