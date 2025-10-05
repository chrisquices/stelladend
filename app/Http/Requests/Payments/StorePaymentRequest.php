<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'plan' => ['required', 'string', 'in:yearly,lifetime'],
        ];
    }
}
