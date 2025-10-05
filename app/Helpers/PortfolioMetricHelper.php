<?php

namespace App\Helpers;

use App\Http\Controllers\Controller;
use App\Services\FxRateService;

class PortfolioMetricHelper extends Controller
{
    public static function formatPortfolioMetric($portfolioMetric, $holdings)
    {
        // Holdings Count
        $holdingsCount = floatval(count($holdings));
        $holdingsCountFormatted = NumberHelper::formatNumberWithSeparators($holdingsCount);

        // Current Cash Available
        $currentCashAvailable = floatval($portfolioMetric->current_cash_available);
        $currentCashAvailableFormatted = NumberHelper::formatNumberToDollar($currentCashAvailable);

        // ----------------------------------------------------------------------------------

        // Current Total Portfolio Value
        $currentTotalPortfolioValue = $holdings->sum('currentMarketValue') + floatval($portfolioMetric->current_cash_available);
        $currentTotalPortfolioValueFormatted = NumberHelper::formatNumberToDollar($currentTotalPortfolioValue);

        // Current Market Value
        $currentMarketValue = $holdings->sum('currentMarketValue');
        $currentMarketValueFormatted = NumberHelper::formatNumberToDollar($currentMarketValue);

        // Current Average Market Value
        $currentAverageDividendYield = $holdings->avg('currentDividendYield');
        $currentAverageDividendYieldFormatted = NumberHelper::formatNumberToPercentage($currentAverageDividendYield);

        // Current Gain Loss In Dollar
        $currentGainLossInDollar = $currentMarketValue - ($holdings->sum('currentCostBasis') * -1);
        $currentGainLossInDollarFormatted = NumberHelper::formatNumberToDollar($currentGainLossInDollar);

        // Current Gain Loss In Percentage
        $costBasis = $holdings->sum('currentCostBasis') * -1;
        $currentGainLossInPercentage = $costBasis != 0
            ? ($currentGainLossInDollar / $costBasis) * 100
            : null;
        $currentGainLossInPercentageFormatted = NumberHelper::formatNumberToPercentage($currentGainLossInPercentage);

        // Current Annual Income
        $currentAnnualIncome = $holdings->sum('currentAnnualIncome');
        $currentAnnualIncomeFormatted = NumberHelper::formatNumberToDollar($currentAnnualIncome);

        // Current Monthly Income
        $currentMonthlyIncome = $holdings->sum('currentMonthlyIncome');
        $currentMonthlyIncomeFormatted = NumberHelper::formatNumberToDollar($currentMonthlyIncome);

        $data = [
            // Holdings Count
            'holdingsCount'                 => $holdingsCount,
            'holdingsCountFormatted'        => $holdingsCountFormatted,

            // Current Cash Available
            'currentCashAvailable'                 => $currentCashAvailable,
            'currentCashAvailableFormatted'        => $currentCashAvailableFormatted,

            // ----------------------------------------------------------------------------------

            // Current Total Portfolio Value
            'currentTotalPortfolioValue'           => $currentTotalPortfolioValue,
            'currentTotalPortfolioValueFormatted'  => $currentTotalPortfolioValueFormatted,

            // Current Market Value
            'currentMarketValue'                   => $currentMarketValue,
            'currentMarketValueFormatted'          => $currentMarketValueFormatted,

            // Current Average Dividend Yield
            'currentAverageDividendYield'            => $currentAverageDividendYield,
            'currentAverageDividendYieldFormatted'   => $currentAverageDividendYieldFormatted,

            // Current Gain Loss In Dollar
            'currentGainLossInDollar'              => $currentGainLossInDollar,
            'currentGainLossInDollarFormatted'     => $currentGainLossInDollarFormatted,

            // Current Gain Loss In Percentage
            'currentGainLossInPercentage'          => $currentGainLossInPercentage,
            'currentGainLossInPercentageFormatted' => $currentGainLossInPercentageFormatted,

            // Current Annual Income
            'currentAnnualIncome'                  => $currentAnnualIncome,
            'currentAnnualIncomeFormatted'         => $currentAnnualIncomeFormatted,

            // Current Monthly Income
            'currentMonthlyIncome'                 => $currentMonthlyIncome,
            'currentMonthlyIncomeFormatted'        => $currentMonthlyIncomeFormatted,
        ];

        // Secondary Currency ------------------------------------------------------------------------------------------------------------------------------------------------------------
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        if ($secondaryCurrency) {

            // Current Cash Available
            $currentCashAvailable = floatval($portfolioMetric->current_cash_available) * $multiplier;
            $currentCashAvailableFormatted = NumberHelper::formatNumberToDollar($currentCashAvailable, $secondaryCurrency);

            // ----------------------------------------------------------------------------------

            // Current Total Portfolio Value
            $currentTotalPortfolioValue = ($holdings->sum('currentMarketValue') * $multiplier) + (floatval($portfolioMetric->current_cash_available) * $multiplier);
            $currentTotalPortfolioValueFormatted = NumberHelper::formatNumberToDollar($currentTotalPortfolioValue, $secondaryCurrency);

            // Current Market Value
            $currentMarketValue = $holdings->sum('currentMarketValue') * $multiplier;
            $currentMarketValueFormatted = NumberHelper::formatNumberToDollar($currentMarketValue, $secondaryCurrency);

            // Current Gain Loss In Dollar
            $currentGainLossInDollar = $currentMarketValue - (($holdings->sum('currentCostBasis') * -1) * $multiplier);
            $currentGainLossInDollarFormatted = NumberHelper::formatNumberToDollar($currentGainLossInDollar, $secondaryCurrency);

            // Current Gain Loss In Percentage
            $denominator = ($holdings->sum('currentCostBasis') * -1) * $multiplier;
            $currentGainLossInPercentage = $denominator != 0
                ? ($currentGainLossInDollar / $denominator) * 100
                : null;
            $currentGainLossInPercentageFormatted = NumberHelper::formatNumberToPercentage($currentGainLossInPercentage);

            // Current Annual Income
            $currentAnnualIncome = $holdings->sum('currentAnnualIncome') * $multiplier;
            $currentAnnualIncomeFormatted = NumberHelper::formatNumberToDollar($currentAnnualIncome, $secondaryCurrency);

            // Current Monthly Income
            $currentMonthlyIncome = $holdings->sum('currentMonthlyIncome') * $multiplier;
            $currentMonthlyIncomeFormatted = NumberHelper::formatNumberToDollar($currentMonthlyIncome, $secondaryCurrency);

            $data['secondaryCurrency'] = [
                // Current Cash Available
                'currentCashAvailable'                 => $currentCashAvailable,
                'currentCashAvailableFormatted'        => $currentCashAvailableFormatted,

                // ----------------------------------------------------------------------------------

                // Current Total Portfolio Value
                'currentTotalPortfolioValue'           => $currentTotalPortfolioValue,
                'currentTotalPortfolioValueFormatted'  => $currentTotalPortfolioValueFormatted,

                // Current Market Value
                'currentMarketValue'                   => $currentMarketValue,
                'currentMarketValueFormatted'          => $currentMarketValueFormatted,

                // Current Gain Loss In Dollar
                'currentGainLossInDollar'              => $currentGainLossInDollar,
                'currentGainLossInDollarFormatted'     => $currentGainLossInDollarFormatted,

                // Current Gain Loss In Percentage
                'currentGainLossInPercentage'          => $currentGainLossInPercentage,
                'currentGainLossInPercentageFormatted' => $currentGainLossInPercentageFormatted,

                // Current Annual Income
                'currentAnnualIncome'                  => $currentAnnualIncome,
                'currentAnnualIncomeFormatted'         => $currentAnnualIncomeFormatted,

                // Current Monthly Income
                'currentMonthlyIncome'                 => $currentMonthlyIncome,
                'currentMonthlyIncomeFormatted'        => $currentMonthlyIncomeFormatted,
            ];
        }

        return $data;
    }
}

