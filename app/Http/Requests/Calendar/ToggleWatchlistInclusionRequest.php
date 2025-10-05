<?php

namespace App\Http\Requests\Calendar;

use App\Helpers\CalendarHelper;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToggleWatchlistInclusionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'watchlistUid' => 'required|exists:watchlists,uid',
        ];
    }
}
