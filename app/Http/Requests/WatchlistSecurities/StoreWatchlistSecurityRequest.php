<?php

namespace App\Http\Requests\WatchlistSecurities;

use Illuminate\Foundation\Http\FormRequest;

class StoreWatchlistSecurityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'securityUid' => ['required', 'exists:securities,uid'],
        ];
    }
}
