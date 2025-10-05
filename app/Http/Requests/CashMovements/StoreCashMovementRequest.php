<?php

namespace App\Http\Requests\CashMovements;

use App\Models\Portfolio;
use App\Models\Security;
use App\Models\TransactionMetric;
use Illuminate\Foundation\Http\FormRequest;

class StoreCashMovementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'portfolioUid' => ['required', 'exists:portfolios,uid'],
            'type'         => ['required', 'string', 'max:255', 'in:deposit,withdrawal'],
            'amount'       => ['required', 'numeric'],
            'note'         => ['nullable', 'string', 'max:255'],
            'date'         => ['required', 'date'],
        ];
    }
}
