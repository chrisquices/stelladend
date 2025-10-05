<?php

namespace App\Helpers;

use Carbon\Carbon;

class CashFlowHelper
{
    public static function calculatePeriodsFromCashFlows($cashFlows)
    {
        return collect($cashFlows)
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date))
            ->map(function ($carbonDate) {
                return [
                    'year' => $carbonDate->year,
                    'month' => $carbonDate->format('F'),
                    'label' => $carbonDate->format('F Y'),
                    'sort_key' => $carbonDate->format('Y-m'),
                ];
            })
            ->unique('sort_key')
            ->sortBy('sort_key')
            ->values()
            ->map(function ($item) {
                unset($item['sort_key']);
                return $item;
            })
            ->all();
    }

    public static function sortHoldings($holdings, $sortBy, $sortDirection)
    {
        $holdings = collect($holdings);

        if ($sortDirection === 'desc') {
            $holdings = $holdings->sortByDesc($sortBy)->values();
        } else {
            $holdings = $holdings->sortBy($sortBy)->values();
        }

        return $holdings;
    }
}
