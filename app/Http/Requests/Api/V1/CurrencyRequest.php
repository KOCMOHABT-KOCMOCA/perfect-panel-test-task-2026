<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'method' => 'required|in:rates,convert',
            'currency' => 'nullable|string',
            'currency_from' => 'required_if:method,convert|string',
            'currency_to' => 'required_if:method,convert|string',
            'value' => 'required_if:method,convert|numeric|min:0.01',
        ];
    }
}
