<?php

namespace App\Http\Requests\Transactions;

use App\Models\Portfolio;
use App\Models\Security;
use App\Models\TransactionMetric;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transactionId' => ['required', 'exists:transactions,id'],
            'note'          => ['nullable', 'string', 'max:255'],
        ];
    }
}
