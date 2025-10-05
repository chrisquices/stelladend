<?php

namespace App\Http\Requests\Watchlists;

use Illuminate\Foundation\Http\FormRequest;

class ShowWatchlistRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sortBy'        => ['nullable', 'string', 'in:ticker,nextExDividendDate,nextPaymentDate,dividendPayoutFrequency,currentDividendYield,currentDividendPerShare,currentMarketPrice,currentChangeInDollar'],
            'sortDirection' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}
