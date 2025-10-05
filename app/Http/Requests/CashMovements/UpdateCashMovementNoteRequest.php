<?php

namespace App\Http\Requests\CashMovements;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashMovementNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cashMovementUid' => ['required', 'exists:cash_movements,uid'],
            'note'            => ['nullable', 'string', 'max:255'],
        ];
    }
}
