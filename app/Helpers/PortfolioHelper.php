<?php

namespace App\Helpers;

use App\Models\Portfolio;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PortfolioHelper
{
    public static function convertPortfolioPerformanceRangeToDate($portfolioPerformanceRange)
    {
        $now = Carbon::now();

        switch ($portfolioPerformanceRange) {
            case '1m':
                return $now->copy()->subMonth()->format('Y-m-d');
            case '3m':
                return $now->copy()->subMonths(3)->format('Y-m-d');
            case '6m':
                return $now->copy()->subMonths(6)->format('Y-m-d');
            case '1y':
                return $now->copy()->subYear()->format('Y-m-d');
            case '2y':
                return $now->copy()->subYears(2)->format('Y-m-d');
            case '5y':
                return $now->copy()->subYears(5)->format('Y-m-d');
            case 'ytd':
                return $now->copy()->startOfYear()->format('Y-m-d');
            case 'max':
                return $now->copy()->subYears(99)->format('Y-m-d');
            default:
                return $now->copy()->subYears(99)->format('Y-m-d');
        }
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

    public static function sortDividendsReceived($dividendsReceived, $sortBy, $sortDirection)
    {
        $dividendsReceived = collect($dividendsReceived);

        if ($sortDirection === 'desc') {
            $dividendsReceived = $dividendsReceived->sortByDesc($sortBy)->values();
        } else {
            $dividendsReceived = $dividendsReceived->sortBy($sortBy)->values();
        }

        return $dividendsReceived;
    }

    public static function sortCashMovements($cashMovements, $sortBy, $sortDirection)
    {
        $cashMovements = collect($cashMovements);

        if ($sortDirection === 'desc') {
            $cashMovements = $cashMovements->sortByDesc($sortBy)->values();
        } else {
            $cashMovements = $cashMovements->sortBy($sortBy)->values();
        }

        return $cashMovements;
    }

    public static function generatePublicReferenceCode()
    {
        do {
            $publicReferenceCode = Str::lower(Str::random(6));
        } while (Portfolio::where('public_reference_code', $publicReferenceCode)->exists());

        return $publicReferenceCode;
    }
}
