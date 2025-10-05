<?php

namespace App\Http\Requests\Portfolios;

use App\Models\Portfolio;
use App\Models\Security;
use App\Models\TransactionMetric;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'isDefault' => ['boolean'],
            'isPublic'  => ['boolean'],
        ];
    }
}
