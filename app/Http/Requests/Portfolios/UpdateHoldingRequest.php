<?php

namespace App\Http\Requests\Portfolios;

use App\Models\Portfolio;
use App\Models\Security;
use App\Models\TransactionMetric;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHoldingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'portfolioUid' => ['required', 'exists:portfolios,uid'],
            'holdings' => ['required', 'array'],
        ];
    }
}
