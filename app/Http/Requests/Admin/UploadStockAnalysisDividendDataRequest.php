<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadStockAnalysisDividendDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'securityUid' => ['required', 'exists:securities,uid'],
            'file'        => ['required']
        ];
    }
}
