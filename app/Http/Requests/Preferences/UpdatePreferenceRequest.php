<?php

namespace App\Http\Requests\Preferences;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'key'   => ['required'],
            'value' => ['nullable'],
        ];
    }
}

