<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\Account;
use App\Models\Portfolio;

class DashboardService
{
    public function getPortfoliosAndAccounts($portfolios)
    {
        $user = auth()->user();
        $secondaryCurrency = $user->preferences->secondaryCurrency;

        // Get FX rates like: [ 'PYG' => 7877.1976, 'EUR' => 0.8538, ... ]
        $fxRates = app(FxRateService::class)->getLatestFxRates();

        // 1. Map Accounts
        $accounts = Account::query()
            ->where('user_id', $user->id)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($account) use ($fxRates, $secondaryCurrency) {

                // Primary Currency
                if ($account->currency->code === 'USD') {
                    $balance = $account->balance;
                    $balanceFormatted = NumberHelper::formatNumberToDollar($balance);
                } else {
                    $relevantFxRate = $fxRates->where('to_currency', $account->currency->code)->first();

                    $balance = $account->balance / $relevantFxRate['rate'];
                    $balanceFormatted = NumberHelper::formatNumberToDollar($balance);
                }

                // Secondary Currency
                if ($secondaryCurrency && $account->currency->code === 'USD') {
                    $relevantFxRate = $fxRates->where('to_currency', $secondaryCurrency->code)->first();

                    $secondaryCurrencyData = [
                        'currencyCode'     => $secondaryCurrency->code,
                        'balance'          => $account->balance * $relevantFxRate['rate'],
                        'balanceFormatted' => NumberHelper::formatNumberToDollar($account->balance * $relevantFxRate['rate'], $secondaryCurrency),
                    ];
                } else {
                    $secondaryCurrencyData = [
                        'currencyCode'     => $account->currency->code,
                        'balance'          => floatval($account->balance),
                        'balanceFormatted' => NumberHelper::formatNumberToDollar($account->balance, $secondaryCurrency),
                    ];
                }

                return [
                    'type'            => 'account',
                    'name'            => $account->name,
                    'balance'          => $balance,
                    'balanceFormatted' => $balanceFormatted,
                    ...($secondaryCurrencyData ? ['secondaryCurrency' => $secondaryCurrencyData] : []),
                ];
            });

        // 2. Map Portfolios
        $portfolioAccounts = collect($portfolios)->map(function ($portfolio) use ($secondaryCurrency) {
            $secondaryCurrencyData = [];

            if ($secondaryCurrency) {
                $secondaryCurrencyData = [
                    'currencyCode' => $secondaryCurrency->code,

                    'balance'          => $portfolio['metrics']['secondaryCurrency']['currentTotalPortfolioValue'],
                    'balanceFormatted' => $portfolio['metrics']['secondaryCurrency']['currentTotalPortfolioValueFormatted'],
                ];
            }

            return [
                'type'            => 'portfolio',
                'name'            => $portfolio['name'],
                'balance'          => floatval($portfolio['metrics']['currentTotalPortfolioValue']),
                'balanceFormatted' => $portfolio['metrics']['currentTotalPortfolioValueFormatted'],
                ...($secondaryCurrencyData ? ['secondaryCurrency' => $secondaryCurrencyData] : []),
            ];
        });

        // 3. Merge and return both
        return $portfolioAccounts->concat($accounts)->values();
    }

    public function getPortfolios()
    {
        $user = auth()->user();

        return Portfolio::query()
            ->select('id', 'uid', 'name')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($portfolio) {

                $holdings = app(PortfolioService::class)->getHoldings($portfolio->id);

                $portfolioMetrics = app(PortfolioMetricService::class)->getPortfolioMetrics($portfolio->id, $holdings);

                return [
                    'uid'      => $portfolio->uid,
                    'name'     => $portfolio->name,
                    'metrics'  => $portfolioMetrics,
                    'holdings' => $holdings,
                ];
            });
    }

    public function getHasBeenWelcomed()
    {
        return auth()->user()->preferences->has_been_welcomed;
    }

    public function hasSecondaryCurrency()
    {
        return auth()->user()->preferences->secondaryCurrency ? true : false;
    }
}
