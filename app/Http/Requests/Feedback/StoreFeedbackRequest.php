<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'source'  => ['required', 'string'],
        ];
    }
}
