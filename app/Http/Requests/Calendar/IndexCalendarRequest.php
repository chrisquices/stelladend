<?php

namespace App\Http\Requests\Calendar;

use App\Helpers\CalendarHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCalendarRequest extends FormRequest
{
    public function rules(): array
    {
        $validPeriods = collect(CalendarHelper::generatePeriods())->pluck('period')->all();

        return [
            'period' => [
                'nullable',
                'string',
                Rule::in($validPeriods),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'period.in' => 'Invalid period',
        ];
    }
}
