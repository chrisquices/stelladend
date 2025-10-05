<?php

namespace App\Helpers;

use App\Services\FxRateService;

class WatchlistHelper
{
    public static function sortHoldings($watchlistSecurities, $sortBy, $sortDirection)
    {
        $watchlistSecurities = collect($watchlistSecurities);

        if ($sortDirection === 'desc') {
            $watchlistSecurities = $watchlistSecurities->sortByDesc($sortBy)->values();
        } else {
            $watchlistSecurities = $watchlistSecurities->sortBy($sortBy)->values();
        }

        return $watchlistSecurities;
    }

    public static function calculateMetrics($watchlistSecurities)
    {
        $dividendYields = [];
        $dividendPerShares = [];
        $changeInDollars = [];
        $changeInPercentages = [];

        foreach ($watchlistSecurities as $security) {
            // Collect dividend yields
            if (isset($security['currentDividendYield'])) {
                $dividendYields[] = $security['currentDividendYield'];
            }

            // Collect dividend per share
            if (isset($security['currentDividendPerShare'])) {
                $dividendPerShares[] = $security['currentDividendPerShare'];
            }

            // Collect dollar and percentage changes
            if (isset($security['currentChangeInDollar'])) {
                $changeInDollars[] = $security['currentChangeInDollar'];
            }

            if (isset($security['currentChangeInPercentage'])) {
                $changeInPercentages[] = $security['currentChangeInPercentage'];
            }
        }

        // Dividend Yield Range
        $minYield = !empty($dividendYields) ? min($dividendYields) : 0;
        $maxYield = !empty($dividendYields) ? max($dividendYields) : 0;
        $dividendYieldRange = (!empty($dividendYields))
            ? NumberHelper::formatNumberToPercentage($minYield) . ' - ' . NumberHelper::formatNumberToPercentage($maxYield)
            : '-';

        // Average Dividend Yield
        $averageDividendYield = !empty($dividendYields) ? array_sum($dividendYields) / count($dividendYields) : 0;
        $averageDividendYieldFormatted = NumberHelper::formatNumberToPercentage($averageDividendYield);

        // Top Dividend Per Share
        $topDividendPerShare = !empty($dividendPerShares) ? max($dividendPerShares) : 0;
        $topDividendPerShareFormatted = NumberHelper::formatNumberToDollar($topDividendPerShare);

        // Average Change in Dollar
        $averageChangeInDollar = !empty($changeInDollars) ? array_sum($changeInDollars) / count($changeInDollars) : 0;
        $averageChangeInDollarFormatted = NumberHelper::formatNumberToDollar($averageChangeInDollar);

        // Average Change in Percentage
        $averageChangeInPercentage = !empty($changeInPercentages) ? array_sum($changeInPercentages) / count($changeInPercentages) : 0;
        $averageChangeInPercentageFormatted = NumberHelper::formatNumberToPercentage($averageChangeInPercentage);

        $data = [
            'dividendYieldRange'                   => $dividendYieldRange,
            'averageDividendYield'                 => $averageDividendYield,
            'averageDividendYieldFormatted'        => $averageDividendYieldFormatted,
            'topDividendPerShare'                  => $topDividendPerShare,
            'topDividendPerShareFormatted'         => $topDividendPerShareFormatted,
            'averageChangeInDollar'         => $averageChangeInDollar,
            'averageChangeInDollarFormatted'=> $averageChangeInDollarFormatted,
            'averageChangeInPercentage'     => $averageChangeInPercentage,
            'averageChangeInPercentageFormatted' => $averageChangeInPercentageFormatted,
        ];

        // Secondary Currency
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        if ($secondaryCurrency) {
            $topDividendPerShareSecondary = $topDividendPerShare * $multiplier;
            $topDividendPerShareFormattedSecondary = NumberHelper::formatNumberToDollar($topDividendPerShareSecondary, $secondaryCurrency);

            $averageChangeInDollarSecondary = $averageChangeInDollar * $multiplier;
            $averageChangeInDollarFormattedSecondary = NumberHelper::formatNumberToDollar($averageChangeInDollarSecondary, $secondaryCurrency);

            $data['secondaryCurrency'] = [
                'topDividendPerShare'                  => $topDividendPerShareSecondary,
                'topDividendPerShareFormatted'         => $topDividendPerShareFormattedSecondary,
                'averageChangeInDollar'         => $averageChangeInDollarSecondary,
                'averageChangeInDollarFormatted'=> $averageChangeInDollarFormattedSecondary,
                // Percentage doesn't change with currency, so no need to duplicate it here
            ];
        }

        return $data;
    }
}
