<?php

namespace App\Http\Requests\Portfolios;

use Illuminate\Foundation\Http\FormRequest;

class ComparePortfolioRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'portfolio1Uid' => ['required', 'exists:portfolios,uid'],
            'portfolio2Uid' => ['nullable', 'exists:portfolios,uid'],
        ];
    }
}
