<?php

namespace App\Helpers;

use App\Services\FxRateService;

class DashboardHelper
{
    public static function generateCompositionChartData($holdings)
    {
        $chartData = [];

        // Filter out holdings with zero market value
        $activeHoldings = $holdings->filter(function ($stock) {
            return $stock['currentMarketValue'] > 0;
        });

        foreach ($activeHoldings as $stock) {
            $chartData[] = [
                'label'                       => $stock['ticker'],
                'color'                       => $stock['color'],
                'currentSharesOwned'          => $stock['currentSharesOwned'],
                'currentSharesOwnedFormatted' => $stock['currentSharesOwnedFormatted'],
                'currentMarketValue'          => $stock['currentMarketValue'],
                'currentMarketValueFormatted' => $stock['currentMarketValueFormatted']
            ];
        }

        return $chartData;
    }

    public static function calculateAccountStats($portfolios, $portfoliosAndAccounts)
    {
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        // Combined Assets Value
        $combinedAssetsValue = floatval($portfoliosAndAccounts->sum('balance'));
        $combinedAssetsValueFormatted = NumberHelper::formatNumberToDollar($combinedAssetsValue);

        // Combined Portfolio Value
        $combinedPortfolioValue = floatval($portfolios->sum('metrics.currentTotalPortfolioValue'));
        $combinedPortfolioValueFormatted = NumberHelper::formatNumberToDollar($combinedPortfolioValue);

        // Combined Market Value
        $combinedMarketValue = floatval($portfolios->sum('metrics.currentMarketValue'));
        $combinedMarketValueFormatted = NumberHelper::formatNumberToDollar($combinedMarketValue);

        // Combined Monthly Income
        $combinedMonthlyIncome = floatval($portfolios->sum('metrics.currentMonthlyIncome'));
        $combinedMonthlyIncomeFormatted = NumberHelper::formatNumberToDollar($combinedMonthlyIncome);

        // Combined Annual Income
        $combinedAnnualIncome = floatval($portfolios->sum('metrics.currentAnnualIncome'));
        $combinedAnnualIncomeFormatted = NumberHelper::formatNumberToDollar($combinedAnnualIncome);

        $data = [
            // Combined Assets Value
            'combinedAssetsValue'          => $combinedAssetsValue,
            'combinedAssetsValueFormatted' => $combinedAssetsValueFormatted,

            // Combined Portfolio Value
            'combinedPortfolioValue'          => $combinedPortfolioValue,
            'combinedPortfolioValueFormatted' => $combinedPortfolioValueFormatted,

            // Combined Market Value
            'combinedMarketValue'            => $combinedMarketValue,
            'combinedMarketValueFormatted'   => $combinedMarketValueFormatted,

            // Combined Monthly Income
            'combinedMonthlyIncome'          => $combinedMonthlyIncome,
            'combinedMonthlyIncomeFormatted' => $combinedMonthlyIncomeFormatted,

            // Combined Annual Income
            'combinedAnnualIncome'           => $combinedAnnualIncome,
            'combinedAnnualIncomeFormatted'  => $combinedAnnualIncomeFormatted,
        ];

        if ($secondaryCurrency) {
            $data['secondaryCurrency'] = [
                // Combined Assets Value
                'combinedAssetsValue'          => floatval($combinedAssetsValue * $multiplier),
                'combinedAssetsValueFormatted' => NumberHelper::formatNumberToDollar($combinedAssetsValue * $multiplier, $secondaryCurrency),

                // Combined Portfolio Value
                'combinedPortfolioValue'          => floatval($combinedPortfolioValue * $multiplier),
                'combinedPortfolioValueFormatted' => NumberHelper::formatNumberToDollar($combinedPortfolioValue * $multiplier, $secondaryCurrency),

                // Combined Market Value
                'combinedMarketValue'             => floatval($combinedMarketValue * $multiplier),
                'combinedMarketValueFormatted'    => NumberHelper::formatNumberToDollar($combinedMarketValue * $multiplier, $secondaryCurrency),

                // Combined Monthly Income
                'combinedMonthlyIncome'          => floatval($combinedMonthlyIncome * $multiplier),
                'combinedMonthlyIncomeFormatted' => NumberHelper::formatNumberToDollar($combinedMonthlyIncome * $multiplier, $secondaryCurrency),

                // Combined Annual Income
                'combinedAnnualIncome'           => floatval($combinedAnnualIncome * $multiplier),
                'combinedAnnualIncomeFormatted'  => NumberHelper::formatNumberToDollar($combinedAnnualIncome * $multiplier, $secondaryCurrency),
            ];
        }

        return $data;
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
