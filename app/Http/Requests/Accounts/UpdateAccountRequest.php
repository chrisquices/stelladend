<?php

namespace App\Http\Requests\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currencyId' => 'required|exists:currencies,id',
            'uid'        => ['required', 'exists:accounts,uid'],
            'name'       => ['required', 'string', 'max:255'],
            'reference'  => ['required', 'string', 'max:255'],
            'balance'    => ['required'],
        ];
    }
}
