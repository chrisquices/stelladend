<?php

namespace App\Http\Requests\Watchlists;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWatchlistRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
        ];
    }
}
