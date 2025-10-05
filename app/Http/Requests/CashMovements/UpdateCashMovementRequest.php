<?php

namespace App\Http\Requests\CashMovements;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashMovementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cashMovementUid' => ['required', 'exists:cash_movements,uid'],
            'type'            => ['required', 'string', 'max:255', 'in:deposit,withdrawal'],
            'amount'          => ['required', 'numeric'],
            'note'            => ['nullable', 'string', 'max:255'],
            'date'            => ['required', 'date'],
        ];
    }
}
