<?php

namespace App\Services;

use App\Helpers\CashMovementHelper;
use App\Helpers\NumberHelper;
use App\Helpers\PortfolioHelper;
use App\Models\CashMovement;
use App\Models\Dividend;
use App\Models\DividendReceived;
use App\Models\LatestMarketQuote;
use App\Models\MarketDate;
use App\Models\MarketQuote;
use App\Models\Portfolio;
use App\Models\PortfolioMetric;
use App\Models\Security;
use App\Models\Transaction;
use App\Models\TransactionMetric;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use function PHPSTORM_META\map;

class PortfolioService
{
    public function setDefaultPortfolioIfNoneExists()
    {
        $user = auth()->user();

        $portfolios = Portfolio::where('user_id', $user->id)->get();

        if ($portfolios) {
            $hasDefaultPortfolio = $portfolios->where('is_default', true);

            if (!$hasDefaultPortfolio->count()) {
                $defaultPortfolio = $portfolios->first();
                $defaultPortfolio->update([
                    'is_default' => true
                ]);
            }
        }
    }

    public function getDefaultPortfolio()
    {
        $user = auth()->user();

        $this->setDefaultPortfolioIfNoneExists();

        return Portfolio::query()
            ->where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
    }

    public function getPortfolios()
    {
        $user = auth()->user();

        $query = Portfolio::query()->where('user_id', $user->id);

        if (!$user->is_premium) {
            $query->where('is_default', true);
        }

        return $query
            ->orderByDesc('is_default')
            ->get()
            ->map(function ($portfolio) {
                return [
                    'uid'       => $portfolio->uid,
                    'name'      => $portfolio->name,
                    'mode'      => $portfolio->mode,
                    'isDefault' => $portfolio->is_default,
                ];
            });
    }

    public function getPortfoliosForComparison($portfolio1Id, $portfolio2Id)
    {
        $user = auth()->user();

        $portfolio1 = Portfolio::query()
            ->where('id', $portfolio1Id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $portfolio2 = Portfolio::query()
            ->where('id', $portfolio2Id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $portfolio1 = $this->getPortfolio($portfolio1->id);
        $portfolio2 = $this->getPortfolio($portfolio2->id);

        $rawPortfolios = [$portfolio1, $portfolio2];
        $portfolios = [];

        foreach ($rawPortfolios as $p) {
            $entry = [];

            $entry['name'] = $p['name'];

            $entry['currentCashAvailable'] = $p['metrics']['currentCashAvailable'];
            $entry['currentCashAvailableFormatted'] = $p['metrics']['currentCashAvailableFormatted'];

            $entry['currentTotalPortfolioValue'] = $p['metrics']['currentTotalPortfolioValue'];
            $entry['currentTotalPortfolioValueFormatted'] = $p['metrics']['currentTotalPortfolioValueFormatted'];

            $entry['currentMarketValue'] = $p['metrics']['currentMarketValue'];
            $entry['currentMarketValueFormatted'] = $p['metrics']['currentMarketValueFormatted'];

            $entry['currentAverageDividendYield'] = $p['metrics']['currentAverageDividendYield'];
            $entry['currentAverageDividendYieldFormatted'] = $p['metrics']['currentAverageDividendYieldFormatted'];

            $entry['currentGainLossInDollar'] = $p['metrics']['currentGainLossInDollar'];
            $entry['currentGainLossInDollarFormatted'] = $p['metrics']['currentGainLossInDollarFormatted'];

            $entry['currentGainLossInPercentage'] = $p['metrics']['currentGainLossInPercentage'];
            $entry['currentGainLossInPercentageFormatted'] = $p['metrics']['currentGainLossInPercentageFormatted'];

            $entry['currentAnnualIncome'] = $p['metrics']['currentAnnualIncome'];
            $entry['currentAnnualIncomeFormatted'] = $p['metrics']['currentAnnualIncomeFormatted'];

            $entry['currentMonthlyIncome'] = $p['metrics']['currentMonthlyIncome'];
            $entry['currentMonthlyIncomeFormatted'] = $p['metrics']['currentMonthlyIncomeFormatted'];

            $entry['holdingsCount'] = count($p['holdings']);

            $entry['holdings'] = $p['holdings']->map(function ($holding) {
                return [
                    'ticker'                         => $holding['ticker'],
                    'name'                           => $holding['name'],
                    'logoUrl'                        => $holding['logoUrl'],
                    'percentageOfPortfolio'          => $holding['percentageOfPortfolio'] ?? 0,
                    'percentageOfPortfolioFormatted' => $holding['percentageOfPortfolioFormatted'] ?? '-',
                ];
            })->values()->toArray();

            $portfolios[] = $entry;
        }

        $difference = [
            'currentCashAvailable'          => $portfolios[0]['currentCashAvailable'] - $portfolios[1]['currentCashAvailable'],
            'currentCashAvailableFormatted' => NumberHelper::formatNumberToDollar($portfolios[0]['currentCashAvailable'] - $portfolios[1]['currentCashAvailable']),

            'currentTotalPortfolioValue'          => $portfolios[0]['currentTotalPortfolioValue'] - $portfolios[1]['currentTotalPortfolioValue'],
            'currentTotalPortfolioValueFormatted' => NumberHelper::formatNumberToDollar($portfolios[0]['currentTotalPortfolioValue'] - $portfolios[1]['currentTotalPortfolioValue']),

            'currentMarketValue'          => $portfolios[0]['currentMarketValue'] - $portfolios[1]['currentMarketValue'],
            'currentMarketValueFormatted' => NumberHelper::formatNumberToDollar($portfolios[0]['currentMarketValue'] - $portfolios[1]['currentMarketValue']),

            'currentAverageDividendYield'          => $portfolios[0]['currentAverageDividendYield'] - $portfolios[1]['currentAverageDividendYield'],
            'currentAverageDividendYieldFormatted' => NumberHelper::formatNumberToPercentage($portfolios[0]['currentAverageDividendYield'] - $portfolios[1]['currentAverageDividendYield']),

            'currentGainLossInDollar'          => $portfolios[0]['currentGainLossInDollar'] - $portfolios[1]['currentGainLossInDollar'],
            'currentGainLossInDollarFormatted' => NumberHelper::formatNumberToDollar($portfolios[0]['currentGainLossInDollar'] - $portfolios[1]['currentGainLossInDollar']),

            'currentGainLossInPercentage'          => $portfolios[0]['currentGainLossInPercentage'] - $portfolios[1]['currentGainLossInPercentage'],
            'currentGainLossInPercentageFormatted' => NumberHelper::formatNumberToPercentage($portfolios[0]['currentGainLossInPercentage'] - $portfolios[1]['currentGainLossInPercentage']),

            'currentAnnualIncome'          => $portfolios[0]['currentAnnualIncome'] - $portfolios[1]['currentAnnualIncome'],
            'currentAnnualIncomeFormatted' => NumberHelper::formatNumberToDollar($portfolios[0]['currentAnnualIncome'] - $portfolios[1]['currentAnnualIncome']),

            'currentMonthlyIncome'          => $portfolios[0]['currentMonthlyIncome'] - $portfolios[1]['currentMonthlyIncome'],
            'currentMonthlyIncomeFormatted' => NumberHelper::formatNumberToDollar($portfolios[0]['currentMonthlyIncome'] - $portfolios[1]['currentMonthlyIncome']),

            'holdingsCount' => abs($portfolios[0]['holdingsCount'] - $portfolios[1]['holdingsCount']),
        ];

        return [
            'portfolios' => $portfolios,
            'difference' => $difference,
        ];
    }

    public function getPortfolio($portfolioId)
    {
        $portfolio = Portfolio::query()
            ->select('id', 'uid', 'name', 'mode', 'is_default', 'is_public', 'public_reference_code')
            ->where('id', $portfolioId)
            ->first();

        $holdings = $this->getHoldings($portfolioId);

        $portfolioMetrics = app(PortfolioMetricService::class)->getPortfolioMetrics($portfolioId, $holdings);

        return [
            'uid'       => $portfolio->uid,
            'name'      => $portfolio->name,
            'mode'      => $portfolio->mode,
            'isDefault' => $portfolio->is_default,
            'isPublic'  => $portfolio->is_public,
            'publicUrl' => $portfolio->publicUrl,
            'metrics'   => $portfolioMetrics,
            'holdings'  => $holdings,
        ];
    }
    public function getPortfolioPerformance($portfolioId, $portfolioPerformanceRange)
    {
        $requestedStartDate = PortfolioHelper::convertPortfolioPerformanceRangeToDate($portfolioPerformanceRange);

        $firstTransactionDate = Transaction::where('portfolio_id', $portfolioId)->min('trade_date');
        $firstCashDate = CashMovement::where('portfolio_id', $portfolioId)->min('date');

        $earliestActivityDate = collect([$firstTransactionDate, $firstCashDate])
            ->filter()
            ->sort()
            ->first();

        $startingDate = $earliestActivityDate
            ? max($requestedStartDate, Carbon::parse($earliestActivityDate)->subDays(7)->format('Y-m-d'))
            : $requestedStartDate;

        $allTransactions = Transaction::query()
            ->select('security_id', 'type', 'shares', 'trade_date')
            ->where('portfolio_id', $portfolioId)
            ->orderBy('trade_date')
            ->get();

        $allCashMovements = CashMovement::query()
            ->select('amount', 'date')
            ->where('portfolio_id', $portfolioId)
            ->orderBy('date')
            ->get();

        $spySecurity = Security::where('ticker', 'SPY')->first();

        $uniqueSecurityIds = $allTransactions->pluck('security_id')
            ->when($spySecurity, fn($ids) => $ids->push($spySecurity->id))
            ->unique();

        $marketDates = MarketDate::query()
            ->select('date')
            ->where('date', '>=', $startingDate)
            ->orderBy('date')
            ->get()
            ->pluck('date');

        if (Carbon::parse($startingDate)->diffInYears(Carbon::now()) >= 2) {
            $marketDates = $marketDates->filter(fn($_, $index) => $index % 2 === 0)->values();
        }

        $marketQuotes = MarketQuote::query()
            ->select('security_id', 'close', 'date')
            ->whereIn('security_id', $uniqueSecurityIds)
            ->whereBetween('date', [$marketDates->first(), $marketDates->last()])
            ->get()
            ->groupBy('date');

        $performance = [];
        $holdings = [];
        $cashAvailable = 0.0;

        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $fxMultiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        // Apply all transactions prior to the startingDate
        foreach ($allTransactions as $tx) {
            if ($tx->trade_date >= $startingDate) break;
            $typeMap = ['buy' => 1, 'buy_drip' => 1];
            $multiplier = $typeMap[strtolower($tx->type)] ?? -1;
            $holdings[$tx->security_id] = ($holdings[$tx->security_id] ?? 0) + ($multiplier * $tx->shares);
        }

        foreach ($allCashMovements as $cash) {
            if ($cash->date >= $startingDate) break;
            $cashAvailable += $cash->amount;
        }

        $transactionsIndex = $allTransactions->search(fn($t) => $t->trade_date >= $startingDate) ?? 0;
        $cashIndex = $allCashMovements->search(fn($c) => $c->date >= $startingDate) ?? 0;

        $transactionCount = $allTransactions->count();
        $cashMovementsCount = $allCashMovements->count();

        $startingValue = null;
        $endingValue = null;
        $bestDay = null;
        $worstDay = null;
        $maxGain = null;
        $maxLoss = null;

        $previousValue = null;
        $bestDayGainLossInPercentage = null;
        $worstDayGainLossInPercentage = null;

        foreach ($marketDates as $date) {
            $dateFormatted = date('F j, Y', strtotime($date));

            while (
                $transactionsIndex < $transactionCount &&
                $allTransactions[$transactionsIndex]->trade_date <= $date
            ) {
                $tx = $allTransactions[$transactionsIndex];
                $typeMap = ['buy' => 1, 'buy_drip' => 1];
                $multiplier = $typeMap[strtolower($tx->type)] ?? -1;
                $holdings[$tx->security_id] = ($holdings[$tx->security_id] ?? 0) + ($multiplier * $tx->shares);
                $transactionsIndex++;
            }

            while (
                $cashIndex < $cashMovementsCount &&
                $allCashMovements[$cashIndex]->date <= $date
            ) {
                $cash = $allCashMovements[$cashIndex];
                $cashAvailable += $cash->amount;
                $cashIndex++;
            }

            $dayQuotes = $marketQuotes->get($date, collect());
            $marketValue = 0.0;

            foreach ($holdings as $securityId => $shares) {
                if ($shares <= 0) continue;
                $quote = $dayQuotes->firstWhere('security_id', $securityId);
                if ($quote && is_numeric($quote->close)) {
                    $marketValue += $shares * $quote->close;
                }
            }

            $totalPortfolioValue = $marketValue + $cashAvailable;

            if ($previousValue !== null && $previousValue > 0) {
                $dailyChange = ($totalPortfolioValue - $previousValue) / $previousValue * 100;

                if ($bestDayGainLossInPercentage === null || $dailyChange > $bestDayGainLossInPercentage) {
                    $bestDayGainLossInPercentage = $dailyChange;
                }

                if ($worstDayGainLossInPercentage === null || $dailyChange < $worstDayGainLossInPercentage) {
                    $worstDayGainLossInPercentage = $dailyChange;
                }
            }

            $previousValue = $totalPortfolioValue;

            $data = [
                'date'                         => $date,
                'dateFormatted'                => $dateFormatted,
                'totalPortfolioValue'          => $totalPortfolioValue,
                'totalPortfolioValueFormatted' => NumberHelper::formatNumberToDollar($totalPortfolioValue),
                'marketValue'                  => $marketValue,
                'marketValueFormatted'         => NumberHelper::formatNumberToDollar($marketValue),
                'cashAvailable'                => $cashAvailable,
                'cashAvailableFormatted'       => NumberHelper::formatNumberToDollar($cashAvailable),
            ];

            if ($secondaryCurrency) {
                $data['secondaryCurrency'] = [
                    'totalPortfolioValue'          => $totalPortfolioValue * $fxMultiplier,
                    'totalPortfolioValueFormatted' => NumberHelper::formatNumberToDollar($totalPortfolioValue * $fxMultiplier, $secondaryCurrency),
                    'marketValue'                  => $marketValue * $fxMultiplier,
                    'marketValueFormatted'         => NumberHelper::formatNumberToDollar($marketValue * $fxMultiplier, $secondaryCurrency),
                    'cashAvailable'                => $cashAvailable * $fxMultiplier,
                    'cashAvailableFormatted'       => NumberHelper::formatNumberToDollar($cashAvailable * $fxMultiplier, $secondaryCurrency),
                ];
            }

            $performance[] = $data;

            if ($startingValue === null) {
                $startingValue = $totalPortfolioValue;
                $startingValueFormatted = NumberHelper::formatNumberToDollar($startingValue);
            }

            $endingValue = $totalPortfolioValue;
            $endingValueFormatted = NumberHelper::formatNumberToDollar($endingValue);

            if ($maxGain === null || $totalPortfolioValue > $maxGain) {
                $maxGain = $totalPortfolioValue;
                $bestDay = $dateFormatted;
            }

            if ($maxLoss === null || $totalPortfolioValue < $maxLoss) {
                $maxLoss = $totalPortfolioValue;
                $worstDay = $dateFormatted;
            }
        }

        $netDeposits = $allCashMovements
            ->filter(fn($c) => $c->date >= $marketDates->first() && $c->date <= $marketDates->last())
            ->sum('amount');

        $changeInDollar = $endingValue - $startingValue;
        $changeInDollarFormatted = NumberHelper::formatNumberToDollar($changeInDollar);

        $changeInPercentage = $startingValue > 0 ? ($changeInDollar / $startingValue) * 100 : 0;
        if ($startingValue == 0) $changeInPercentage = 100;
        $changeInPercentageFormatted = NumberHelper::formatNumberToPercentage($changeInPercentage);

        $metrics = [
            'changeInDollar'                       => $changeInDollar,
            'changeInDollarFormatted'             => $changeInDollarFormatted,
            'changeInPercentage'                  => $changeInPercentage,
            'changeInPercentageFormatted'         => $changeInPercentageFormatted,
            'startingValue'                       => $startingValue,
            'startingValueFormatted'              => $startingValueFormatted,
            'endingValue'                         => $endingValue,
            'endingValueFormatted'                => $endingValueFormatted,
            'bestDay'                             => $bestDay,
            'worstDay'                            => $worstDay,
            'bestDayGainLossInPercentage'         => $bestDayGainLossInPercentage,
            'bestDayGainLossInPercentageFormatted'=> NumberHelper::formatNumberToPercentage($bestDayGainLossInPercentage),
            'worstDayGainLossInPercentage'        => $worstDayGainLossInPercentage,
            'worstDayGainLossInPercentageFormatted'=> NumberHelper::formatNumberToPercentage($worstDayGainLossInPercentage),
        ];

        if ($secondaryCurrency) {
            $metrics['secondaryCurrency'] = [
                'changeInDollar'          => $changeInDollar * $fxMultiplier,
                'changeInDollarFormatted' => NumberHelper::formatNumberToDollar($changeInDollar * $fxMultiplier, $secondaryCurrency),
                'startingValue'           => $startingValue * $fxMultiplier,
                'startingValueFormatted'  => NumberHelper::formatNumberToDollar($startingValue * $fxMultiplier, $secondaryCurrency),
                'endingValue'             => $endingValue * $fxMultiplier,
                'endingValueFormatted'    => NumberHelper::formatNumberToDollar($endingValue * $fxMultiplier, $secondaryCurrency),
            ];
        }

        return [
            'metrics'   => $metrics,
            'chartData' => $performance,
        ];
    }

    public function getPortfolioByUid($portfolioUid)
    {
        return Portfolio::query()
            ->where('uid', $portfolioUid)
            ->firstOrFail();
    }

    public function storeStarterPortfolio()
    {
        $user = auth()->user();

        $portfolios = Portfolio::where('user_id', $user->id)->exists();

        if (!$portfolios) {
            $portfolio = Portfolio::create([
                'uid'                     => Str::uuid()->toString(),
                'user_id'                 => $user->id,
                'name'                    => 'Default Portfolio',
                'mode'                    => 'advanced',
                'public_reference_code'   => PortfolioHelper::generatePublicReferenceCode(),
                'is_default'              => true,
                'is_public'               => false,
                'is_import_only'          => false,
                'is_included_in_calendar' => true
            ]);

            PortfolioMetric::firstOrCreate([
                'portfolio_id' => $portfolio->id,
            ], [
                'current_cash_available' => 0,
            ]);
        }
    }

    public function getHoldings($portfolioId)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);

        $transactionMetrics = TransactionMetric::query()
            ->with('portfolio', 'security')
            ->select('transaction_metrics.*')
            ->join('securities', 'securities.id', '=', 'transaction_metrics.security_id')
            ->where('portfolio_id', $portfolioId)
            ->where('current_shares_owned', '>', 0)
            ->orderBy('securities.ticker', 'ASC')
            ->get();

        $latestMarketQuotes = LatestMarketQuote::query()
            ->select('security_id', 'close', 'change_in_dollar', 'change_in_percentage')
            ->whereIn('security_id', $transactionMetrics->pluck('security_id'))
            ->get()
            ->keyBy('security_id');

        $dividends = Dividend::query()
            ->select('security_id', 'amount', 'payment_date')
            ->whereIn('security_id', $transactionMetrics->pluck('security_id'))
            ->where('payment_date', '>=', now()->subYear())
            ->orderBy('payment_date', 'desc')
            ->get()
            ->groupBy('security_id');

        $transactions = Transaction::query()
            ->where('portfolio_id', $portfolio->id)
            ->whereIn('security_id', $transactionMetrics->pluck('security_id'))
            ->when($portfolio->is_import_only === true, function ($query) {
                return $query->orderBy('true_order', 'desc');
            })
            ->when($portfolio->is_import_only === false, function ($query) {
                return $query->orderBy('batch', 'desc')->orderBy('order', 'desc');
            })
            ->get()
            ->groupBy('security_id');

        // Total Current Market Value
        $totalCurrentMarketValue = 0;

        foreach ($transactionMetrics as $transactionMetric) {
            $securityMarketQuote = $latestMarketQuotes[$transactionMetric->security_id];

            $totalCurrentMarketValue += $transactionMetric->current_shares_owned * $securityMarketQuote->close;
        }

        // Secondary Currency
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        return $transactionMetrics->map(function ($transactionMetric) use ($portfolio, $totalCurrentMarketValue, $latestMarketQuotes, $dividends, $transactions, $secondaryCurrency, $multiplier) {
            $security = $transactionMetric->security;

            $latestMarketQuote = $latestMarketQuotes[$security->id];

            $dividends = $dividends[$security->id] ?? collect([]);

            // Color
            $color = $security->color;

            // Trend
            $trend = 'neutral';

            if ($latestMarketQuote->change_in_percentage > 0) $trend = 'gainer';
            if ($latestMarketQuote->change_in_percentage < 0) $trend = 'loser';


            // Current Average Price
            $currentAveragePrice = floatval($transactionMetric->current_average_price);
            $currentAveragePriceFormatted = NumberHelper::formatNumberToDollar($currentAveragePrice);

            // Current Cost Basis
            $currentCostBasis = floatval($transactionMetric->current_cost_basis);
            $currentCostBasisFormatted = NumberHelper::formatNumberToDollar($currentCostBasis);

            // Current Shares Owned
            $currentSharesOwned = floatval($transactionMetric->current_shares_owned);
            $currentSharesOwnedFormatted = NumberHelper::formatNumberWithSeparators($currentSharesOwned, NumberHelper::countDecimals($currentSharesOwned));

            // Current Market Price
            $currentMarketPrice = floatval($latestMarketQuote->close);
            $currentMarketPriceFormatted = NumberHelper::formatNumberToDollar($currentMarketPrice);

            // Current Market Value
            $currentMarketValue = floatval($latestMarketQuote->close * $currentSharesOwned);
            $currentMarketValueFormatted = NumberHelper::formatNumberToDollar($currentMarketValue);

            // Change in Dollar
            $currentChangeInDollar = floatval($latestMarketQuote->change_in_dollar);
            $currentChangeInDollarFormatted = NumberHelper::formatNumberToDollar($currentChangeInDollar);

            // Change in Percentage
            $currentChangeInPercentage = floatval($latestMarketQuote->change_in_percentage);
            $currentChangeInPercentageFormatted = NumberHelper::formatNumberToPercentage($currentChangeInPercentage);

            // Current Dividend Yield
            $currentDividendYield = floatval(($dividends->sum('amount') / $latestMarketQuote->close) * 100);;
            $currentDividendYieldFormatted = NumberHelper::formatNumberToPercentage($currentDividendYield, 2);

            // Current Annual Income
            $currentAnnualIncome = floatval($currentMarketValue * ($currentDividendYield / 100));
            $currentAnnualIncomeFormatted = NumberHelper::formatNumberToDollar($currentAnnualIncome);

            // Current Monthly Income
            $currentMonthlyIncome = $currentAnnualIncome / 12;
            $currentMonthlyIncomeFormatted = NumberHelper::formatNumberToDollar($currentMonthlyIncome);

            // Percentage of Portfolio
            $percentageOfPortfolio = ($currentMarketValue / $totalCurrentMarketValue) * 100;
            $percentageOfPortfolioFormatted = NumberHelper::formatNumberToPercentage($percentageOfPortfolio, 0);

            $data = [
                // Color
                'color'                              => $color,

                // Trend
                'trend'                              => $trend,

                // Current Average Price
                'currentAveragePrice'                => $currentAveragePrice,
                'currentAveragePriceFormatted'       => $currentAveragePriceFormatted,

                // Current Cost Basis
                'currentCostBasis'                   => $currentCostBasis,
                'currentCostBasisFormatted'          => $currentCostBasisFormatted,

                // Current Shares Owned
                'currentSharesOwned'                 => $currentSharesOwned,
                'currentSharesOwnedFormatted'        => $currentSharesOwnedFormatted,

                // Current Market Price
                'currentMarketPrice'                 => $currentMarketPrice,
                'currentMarketPriceFormatted'        => $currentMarketPriceFormatted,

                // Current Market Value
                'currentMarketValue'                 => $currentMarketValue,
                'currentMarketValueFormatted'        => $currentMarketValueFormatted,

                // Current Change in Dollar
                'currentChangeInDollar'              => $currentChangeInDollar,
                'currentChangeInDollarFormatted'     => $currentChangeInDollarFormatted,

                // Current Change in Percentage
                'currentChangeInPercentage'          => $currentChangeInPercentage,
                'currentChangeInPercentageFormatted' => $currentChangeInPercentageFormatted,

                // Current Dividend Yield
                'currentDividendYield'               => $currentDividendYield,
                'currentDividendYieldFormatted'      => $currentDividendYieldFormatted,

                // Current Annual Income
                'currentAnnualIncome'                => $currentAnnualIncome,
                'currentAnnualIncomeFormatted'       => $currentAnnualIncomeFormatted,

                // Current Monthly Income
                'currentMonthlyIncome'               => $currentMonthlyIncome,
                'currentMonthlyIncomeFormatted'      => $currentMonthlyIncomeFormatted,

                // Percentage of Portfolio
                'percentageOfPortfolio'              => $percentageOfPortfolio,
                'percentageOfPortfolioFormatted'     => $percentageOfPortfolioFormatted,
            ];

            // SECONDARY CURRENCY -------------------------------------------------------------------------------------------------------------------------------------------------------
            if ($secondaryCurrency) {
                // Current Average Price
                $currentAveragePrice = floatval($transactionMetric->current_average_price * $multiplier);
                $currentAveragePriceFormatted = NumberHelper::formatNumberToDollar($currentAveragePrice, $secondaryCurrency);

                // Current Cost Basis
                $currentCostBasis = floatval($transactionMetric->current_cost_basis * $multiplier);
                $currentCostBasisFormatted = NumberHelper::formatNumberToDollar($currentCostBasis, $secondaryCurrency);

                // Current Market Price
                $currentMarketPrice = floatval($latestMarketQuote->close * $multiplier);
                $currentMarketPriceFormatted = NumberHelper::formatNumberToDollar($currentMarketPrice, $secondaryCurrency);

                // Current Market Value
                $currentMarketValue = floatval(($latestMarketQuote->close * $multiplier) * $currentSharesOwned);
                $currentMarketValueFormatted = NumberHelper::formatNumberToDollar($currentMarketValue, $secondaryCurrency);

                // Current Change in Dollar
                $currentChangeInDollar = floatval($latestMarketQuote->change_in_dollar * $multiplier);
                $currentChangeInDollarFormatted = NumberHelper::formatNumberToDollar($currentChangeInDollar, $secondaryCurrency);

                // Current Annual Income
                $currentAnnualIncome = floatval($currentMarketValue * ($currentDividendYield / 100));
                $currentAnnualIncomeFormatted = NumberHelper::formatNumberToDollar($currentAnnualIncome, $secondaryCurrency);

                // Current Monthly Income
                $currentMonthlyIncome = $currentAnnualIncome / 12;
                $currentMonthlyIncomeFormatted = NumberHelper::formatNumberToDollar($currentMonthlyIncome, $secondaryCurrency);

                $data['secondaryCurrency'] = [
                    // Current Average Price
                    'currentAveragePrice'            => $currentAveragePrice,
                    'currentAveragePriceFormatted'   => $currentAveragePriceFormatted,

                    // Current Cost Basis
                    'currentCostBasis'               => $currentCostBasis,
                    'currentCostBasisFormatted'      => $currentCostBasisFormatted,

                    // Current Market Price
                    'currentMarketPrice'             => $currentMarketPrice,
                    'currentMarketPriceFormatted'    => $currentMarketPriceFormatted,

                    // Current Market Value
                    'currentMarketValue'             => $currentMarketValue,
                    'currentMarketValueFormatted'    => $currentMarketValueFormatted,

                    // Current Change in Dollar
                    'currentChangeInDollar'          => $currentChangeInDollar,
                    'currentChangeInDollarFormatted' => $currentChangeInDollarFormatted,

                    // Current Annual Income
                    'currentAnnualIncome'            => $currentAnnualIncome,
                    'currentAnnualIncomeFormatted'   => $currentAnnualIncomeFormatted,

                    // Current Monthly Income
                    'currentMonthlyIncome'           => $currentMonthlyIncome,
                    'currentMonthlyIncomeFormatted'  => $currentMonthlyIncomeFormatted,
                ];
            }

            // Individual Transactions
            if ($portfolio->mode === 'advanced') {
                $relevantTransactions = $transactions[$security->id]
                    ->map(function ($transaction) use ($secondaryCurrency, $multiplier) {
                        // Transaction
                        $uid = $transaction->uid;

                        // Type
                        $type = $transaction->type;
                        $typeFormatted = $transaction->typeFormatted;

                        // Current Shares At This Point In Time
                        $currentSharesAtThisPointInTime = floatval($transaction->current_shares_at_this_point_in_time);
                        $currentSharesAtThisPointInTimeFormatted = NumberHelper::formatNumberWithSeparators($currentSharesAtThisPointInTime, NumberHelper::countDecimals($currentSharesAtThisPointInTime));

                        // Shares
                        $shares = floatval($transaction->shares);
                        $sharesFormatted = NumberHelper::formatNumberWithSeparators($shares, NumberHelper::countDecimals($shares));

                        // Price
                        $price = floatval($transaction->price);
                        $priceFormatted = NumberHelper::formatNumberToDollar($price);

                        // Fees and Commissions
                        $feesAndCommissions = floatval($transaction->fees_and_commissions);
                        $feesAndCommissionsFormatted = NumberHelper::formatNumberToDollar($feesAndCommissions);

                        // Total
                        $total = floatval(abs($transaction->total));
                        $totalFormatted = NumberHelper::formatNumberToDollar($total);

                        // Trade Value
                        $tradeValue = floatval($transaction->trade_value);
                        $tradeValueFormatted = NumberHelper::formatNumberToDollar($tradeValue);

                        // Gain Loss in Dollar
                        $gainLossInDollar = floatval($transaction->gain_loss_in_dollar);
                        $gainLossInDollarFormatted = NumberHelper::formatNumberToDollar($gainLossInDollar);

                        // Gain Loss in Percentage
                        $gainLossInPercentage = floatval($transaction->gain_loss_in_percentage);
                        $gainLossInPercentageFormatted = NumberHelper::formatNumberToPercentage($gainLossInPercentage);

                        // Note
                        $note = $transaction->note;

                        // Is Imported
                        $isImported = $transaction->is_imported;

                        // Has Entered
                        $hasEntered = ($currentSharesAtThisPointInTime === $shares) ? true : false;

                        // Has Exited
                        $hasExited = $transaction->has_exited;

                        // Trade Date
                        $tradeDate = $transaction->trade_date;
                        $tradeDateFormatted = Carbon::parse($tradeDate)->format('F j, Y');

                        $data = [
                            // Transaction
                            'uid'                                     => $uid,

                            // Type
                            'type'                                    => $type,
                            'typeFormatted'                           => $typeFormatted,

                            // Current Shares At This Point In Time
                            'currentSharesAtThisPointInTime'          => $currentSharesAtThisPointInTime,
                            'currentSharesAtThisPointInTimeFormatted' => $currentSharesAtThisPointInTimeFormatted,

                            // Shares
                            'shares'                                  => $shares,
                            'sharesFormatted'                         => $sharesFormatted,

                            // Price
                            'price'                                   => $price,
                            'priceFormatted'                          => $priceFormatted,

                            // Fees and Commissions
                            'feesAndCommissions'                      => $feesAndCommissions,
                            'feesAndCommissionsFormatted'             => $feesAndCommissionsFormatted,

                            // Total
                            'total'                                   => $total,
                            'totalFormatted'                          => $totalFormatted,

                            // Trade Value
                            'tradeValue'                              => $tradeValue,
                            'tradeValueFormatted'                     => $tradeValueFormatted,

                            // Gain Loss in Dollar
                            'gainLossInDollar'                        => $gainLossInDollar,
                            'gainLossInDollarFormatted'               => $gainLossInDollarFormatted,

                            // Gain Loss in Percentage
                            'gainLossInPercentage'                    => $gainLossInPercentage,
                            'gainLossInPercentageFormatted'           => $gainLossInPercentageFormatted,

                            // Note
                            'note'                                    => $note,

                            // Is Imported
                            'isImported'                              => $isImported,

                            // Has Entered
                            'hasEntered'                              => $hasEntered,

                            // Has Exited
                            'hasExited'                               => $hasExited,

                            // Trade Date
                            'tradeDate'                               => $tradeDate,
                            'tradeDateFormatted'                      => $tradeDateFormatted,
                        ];

                        if ($secondaryCurrency) {
                            // Price
                            $price = floatval($transaction->price * $multiplier);
                            $priceFormatted = NumberHelper::formatNumberToDollar($price, $secondaryCurrency);

                            // Fees and Commissions
                            $feesAndCommissions = floatval($transaction->fees_and_commissions * $multiplier);
                            $feesAndCommissionsFormatted = NumberHelper::formatNumberToDollar($feesAndCommissions, $secondaryCurrency);

                            // Total
                            $total = floatval(abs($transaction->total * $multiplier));
                            $totalFormatted = NumberHelper::formatNumberToDollar($total, $secondaryCurrency);

                            // Trade Value
                            $tradeValue = floatval($transaction->trade_value * $multiplier);
                            $tradeValueFormatted = NumberHelper::formatNumberToDollar($tradeValue, $secondaryCurrency);

                            // Gain Loss in Dollar
                            $gainLossInDollar = floatval($transaction->gain_loss_in_dollar * $multiplier);
                            $gainLossInDollarFormatted = NumberHelper::formatNumberToDollar($gainLossInDollar, $secondaryCurrency);

                            $data['secondaryCurrency'] = [
                                // Price
                                'price'                       => $price,
                                'priceFormatted'              => $priceFormatted,

                                // Fees and Commissions
                                'feesAndCommissions'          => $feesAndCommissions,
                                'feesAndCommissionsFormatted' => $feesAndCommissionsFormatted,

                                // Total
                                'total'                       => $total,
                                'totalFormatted'              => $totalFormatted,

                                // Trade Value
                                'tradeValue'                  => $tradeValue,
                                'tradeValueFormatted'         => $tradeValueFormatted,

                                // Gain Loss in Dollar
                                'gainLossInDollar'            => $gainLossInDollar,
                                'gainLossInDollarFormatted'   => $gainLossInDollarFormatted,
                            ];
                        }

                        return $data;
                    });

                $transactions = [
                    'transactions' => $relevantTransactions,
                ];
            }

            return array_merge(
                [
                    'uid'     => $security->uid,
                    'ticker'  => $security->ticker,
                    'name'    => $security->name,
                    'logoUrl' => $security->logoUrl,
                ],
                $data,
                $portfolio->mode === 'advanced' ? $transactions : []
            );
        });
    }

    public function getThisYearsDividends($portfolioId, $holdings)
    {
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        $securityIds = Security::select('id')->whereIn('uid', collect($holdings)->pluck('uid'))->pluck('id');

        $dividendsReceived = DividendReceived::query()
            ->select('security_id', 'shares', 'dividend_amount', 'total', 'date')
            ->with('dividend', 'security')
            ->where('portfolio_id', $portfolioId)
            ->whereIn('security_id', $securityIds)
            ->whereYear('date', Carbon::now()->year)
            ->orderBy('date', 'DESC')
            ->get()
            ->map(function ($dividendReceived) use ($secondaryCurrency, $multiplier) {
                // Security
                $ticker = $dividendReceived->security->ticker;
                $color = $dividendReceived->security->color;

                // Shares
                $shares = floatval($dividendReceived->shares);
                $sharesFormatted = NumberHelper::formatNumberWithSeparators($shares);

                // Dividend Amount
                $dividendAmount = floatval($dividendReceived->dividend_amount);
                $dividendAmountFormatted = NumberHelper::formatNumberToDollar($dividendAmount);

                // Total
                $total = floatval($dividendReceived->total);
                $totalFormatted = NumberHelper::formatNumberToDollar($total);

                // Date
                $date = Carbon::parse($dividendReceived->date)->timestamp * 1000;
                $dateFormatted = Carbon::parse($dividendReceived->date)->format('F j, Y');

                $data = [
                    // Security
                    'ticker'                  => $ticker,
                    'color'                   => $color,

                    // Shares
                    'shares'                  => $shares,
                    'sharesFormatted'         => $sharesFormatted,

                    // Dividend Amount
                    'dividendAmount'          => $dividendAmount,
                    'dividendAmountFormatted' => $dividendAmountFormatted,

                    // Total
                    'total'                   => $total,
                    'totalFormatted'          => $totalFormatted,

                    // Date
                    'date'                    => $date,
                    'dateFormatted'           => $dateFormatted,
                ];

                // Secondary Currency
                if ($multiplier) {
                    $data['secondaryCurrency'] = [

                        // Dividend Amount
                        'dividendAmount'          => round($dividendAmount * $multiplier, 5),
                        'dividendAmountFormatted' => NumberHelper::formatNumberToDollar($dividendAmount * $multiplier, $secondaryCurrency),

                        // Total
                        'total'                   => round($total * $multiplier, 2),
                        'totalFormatted'          => NumberHelper::formatNumberToDollar($total * $multiplier, $secondaryCurrency),
                    ];
                }

                return $data;
            });

        $futureDividends = Dividend::query()
            ->select('security_id', 'amount', 'payment_date')
            ->with('security')
            ->whereIn('security_id', $securityIds)
            ->whereDate('payment_date', '>', Carbon::today())
            ->orderBy('payment_date')
            ->get()
            ->map(function ($dividend) use ($secondaryCurrency, $multiplier, $holdings) {
                // Security
                $ticker = $dividend->security->ticker;
                $color = $dividend->security->color;

                // Shares
                $shares = collect($holdings)->where('ticker', $dividend->security->ticker)->first()['currentSharesOwned'];
                $sharesFormatted = NumberHelper::formatNumberWithSeparators($shares);

                // Dividend Amount
                $dividendAmount = round($dividend->amount, 5);
                $dividendAmountFormatted = NumberHelper::formatNumberToDollar($dividendAmount);

                // Total
                $total = round($shares * $dividendAmount, 2);
                $totalFormatted = NumberHelper::formatNumberToDollar($total);

                // Date
                $date = Carbon::parse($dividend->payment_date)->timestamp * 1000;
                $dateFormatted = Carbon::parse($dividend->payment_date)->format('F j, Y');

                $data = [
                    // Security
                    'ticker'                  => $ticker,
                    'color'                   => $color,

                    // Shares
                    'shares'                  => $shares,
                    'sharesFormatted'         => $sharesFormatted,

                    // Dividend Amount
                    'dividendAmount'          => $dividendAmount,
                    'dividendAmountFormatted' => $dividendAmountFormatted,

                    // Total
                    'total'                   => $total,
                    'totalFormatted'          => $totalFormatted,

                    // Date
                    'date'                    => $date,
                    'dateFormatted'           => $dateFormatted,
                ];

                if ($multiplier) {
                    $data['secondaryCurrency'] = [

                        // Dividend Amount
                        'dividendAmount'          => round($dividendAmount * $multiplier, 5),
                        'dividendAmountFormatted' => NumberHelper::formatNumberToDollar($dividendAmount * $multiplier, $secondaryCurrency),

                        // Total
                        'total'                   => round($total * $multiplier, 2),
                        'totalFormatted'          => NumberHelper::formatNumberToDollar($total * $multiplier, $secondaryCurrency),
                    ];
                }

                return $data;
            });

        $securities = Security::select('id', 'ticker', 'dividend_payout_frequency')->whereIn('id', $securityIds)->get()->keyBy('ticker');

        // 👇 Place this before the groupedByMonth map
        $latestDividendsBySecurity = \App\Models\Dividend::query()
            ->whereIn('security_id', $securityIds)
            ->whereYear('payment_date', Carbon::now()->year)
            ->orderByDesc('payment_date')
            ->get()
            ->groupBy('security_id')
            ->map(fn($dividends) => $dividends->first());

        $groupedByMonth = collect(range(1, 12))->map(function ($month) {
            return [
                'month'              => Carbon::create()->month($month)->format('F'),
                'dividendsReceived'  => [],
                'futureDividends'    => [],
                'estimatedDividends' => [],
                'totals'             => [],
            ];
        })->map(function ($monthGroup, $index) use ($securities, $latestDividendsBySecurity, $dividendsReceived, $futureDividends, $secondaryCurrency, $multiplier, $holdings) {
            $monthNumber = $index + 1;

            $dividendsForMonth = $dividendsReceived->filter(function ($record) use ($monthNumber) {
                return Carbon::parse($record['dateFormatted'])->month === $monthNumber;
            })->values();

            $futureDivsForMonth = $futureDividends->filter(function ($div) use ($monthNumber) {
                return Carbon::parse($div['dateFormatted'])->month === $monthNumber;
            })->values();

            $monthGroup['dividendsReceived'] = $dividendsForMonth;
            $monthGroup['futureDividends'] = $futureDivsForMonth;

            $monthGroup['estimatedDividends'] = [];

            if ($monthNumber > Carbon::now()->month) {
                $existingTickers = $dividendsForMonth->pluck('ticker')->merge($futureDivsForMonth->pluck('ticker'))->unique();

                $monthGroup['estimatedDividends'] = collect($holdings)->reject(function ($holding) use ($existingTickers) {
                    return $existingTickers->contains($holding['ticker']);
                })->map(function ($holding) use ($securities, $latestDividendsBySecurity, $secondaryCurrency, $multiplier, $monthNumber) {

                    $shares = $holding['currentSharesOwned'] ?? 0;

                    $security = $securities[$holding['ticker']];
                    if (!$security || !$security->dividend_payout_frequency) return null;

                    $lastDividend = $latestDividendsBySecurity[$security->id] ?? null;

                    if (!$lastDividend) return null;

                    $lastMonth = Carbon::parse($lastDividend->payment_date)->month;
                    $interval = 12 / $security->dividend_payout_frequency;

                    $estimatedMonths = collect(range($lastMonth, 12, $interval))
                        ->map(fn($m) => $m > 12 ? $m - 12 : $m)
                        ->unique();

                    if (!$estimatedMonths->contains($monthNumber)) return null;

                    $dividendAmount = round($lastDividend->amount, 5);
                    $dividendAmountFormatted = NumberHelper::formatNumberToDollar($dividendAmount);

                    $estimatedTotal = round($shares * $dividendAmount, 2);
                    $estimatedTotalFormatted = NumberHelper::formatNumberToDollar($estimatedTotal);

                    // Date
                    $date = Carbon::parse($lastDividend->payment_date)->timestamp * 1000;
                    $dateFormatted = Carbon::parse($lastDividend->payment_date)->format('F d, Y');

                    $data = [
                        // Security
                        'ticker'                  => $holding['ticker'],
                        'color'                   => $security->color,

                        // Shares
                        'shares'                  => $shares,
                        'sharesFormatted'         => NumberHelper::formatNumberWithSeparators($shares),

                        // Dividend Amount
                        'dividendAmount'          => $dividendAmount,
                        'dividendAmountFormatted' => $dividendAmountFormatted,

                        // Total
                        'total'                   => $estimatedTotal,
                        'totalFormatted'          => $estimatedTotalFormatted,

                        // Date
                        'date'                    => $date,
                        'dateFormatted'           => $dateFormatted,
                    ];

                    if ($secondaryCurrency) {
                        $data['secondaryCurrency'] = [
                            // Dividend Amount
                            'dividendAmount'          => round($dividendAmount * $multiplier, 5),
                            'dividendAmountFormatted' => NumberHelper::formatNumberToDollar($dividendAmount * $multiplier, $secondaryCurrency),

                            // Total
                            'total'                   => round($estimatedTotal * $multiplier, 2),
                            'totalFormatted'          => NumberHelper::formatNumberToDollar($estimatedTotal * $multiplier, $secondaryCurrency),
                        ];
                    }

                    return $estimatedTotal > 0 ? $data : null;
                })->filter()->values();
            }

            // TOTALS
            $merged = collect()
                ->merge($monthGroup['dividendsReceived'])
                ->merge($monthGroup['futureDividends'])
                ->merge($monthGroup['estimatedDividends']);

            $dividendAmountSum = $merged->sum('dividendAmount');
            $totalSum = $merged->sum('total');

            $monthGroup['totals'] = [
                'dividendAmount'          => round($dividendAmountSum, 5),
                'dividendAmountFormatted' => NumberHelper::formatNumberToDollar($dividendAmountSum),
                'total'                   => round($totalSum, 2),
                'totalFormatted'          => NumberHelper::formatNumberToDollar($totalSum),
                ...($secondaryCurrency ? [
                    'secondaryCurrency' => [
                        'dividendAmount'          => round($dividendAmountSum * $multiplier, 5),
                        'dividendAmountFormatted' => NumberHelper::formatNumberToDollar($dividendAmountSum * $multiplier, $secondaryCurrency),
                        'total'                   => round($totalSum * $multiplier, 2),
                        'totalFormatted'          => NumberHelper::formatNumberToDollar($totalSum * $multiplier, $secondaryCurrency),
                    ]
                ] : []),
            ];

            return $monthGroup;
        });

        return $groupedByMonth->values()->all();
    }

    public function recalculate($portfolioId) // TODO verify all.
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $securityIdsInPortfolio = Transaction::where('portfolio_id', $portfolioId)->distinct()->pluck('security_id')->toArray();
        $securities = Security::find($securityIdsInPortfolio);

        foreach ($securities as $security) {
            $transactions = Transaction::query()
                ->where('portfolio_id', $portfolioId)
                ->where('security_id', $security->id)
                ->orderByRaw("DATE(trade_date) ASC")
                ->orderByRaw("CASE WHEN type = 'purchase' THEN 0 ELSE 1 END")
                ->orderBy('id', 'asc')
                ->get();

            $batch = 1;
            $order = 1;
            $currentSharesOwned = 0;
            $currentCostBasis = 0; // use this

            foreach ($transactions as $transaction) {

                if (!$portfolio->is_import_only) {
                    $transaction->batch = $batch;
                    $transaction->order = $order++;
                }

                $transaction->gain_loss_in_dollar = null;
                $transaction->gain_loss_in_percentage = null;

                if (in_array($transaction->type, ['buy', 'buy_drip'])) {
                    $currentSharesOwned += $transaction->shares;
                    $currentCostBasis += $transaction->shares * $transaction->price;
                    $transaction->has_exited = false;
                }

                if ($transaction->type === 'sell') {
                    $currentSharesOwned -= $transaction->shares;

                    $transaction->has_exited = $currentSharesOwned <= 0;

                    if ($transaction->has_exited) {
                        $batch++;
                        $order = 0;
                    }

                    // === Begin FIFO Gain/Loss Calculation ===

                    $sharesToSell = $transaction->shares;
                    $gainLossInDollar = 0;
                    $totalCostBasis = 0;

                    $previousBuys = $transactions->filter(fn($tx) => in_array($tx->type, ['buy', 'buy_drip']) &&
                        $tx->batch === $transaction->batch &&
                        $tx->order <= $transaction->order
                    );

                    $previousSells = $transactions->filter(fn($tx) => $tx->type === 'sell' &&
                        $tx->batch === $transaction->batch &&
                        $tx->order < $transaction->order
                    );

                    $consumedShares = collect();

                    $previousSells->each(function ($sellTx) use ($previousBuys, &$consumedShares) {
                        $remaining = $sellTx->shares;

                        foreach ($previousBuys as $buyTx) {
                            $used = $consumedShares->get($buyTx->id, 0);
                            $available = $buyTx->shares - $used;

                            if ($available <= 0) continue;

                            $matched = min($available, $remaining);
                            $consumedShares->put($buyTx->id, $used + $matched);

                            $remaining -= $matched;
                            if ($remaining <= 0) break;
                        }
                    });

                    foreach ($previousBuys as $buyTx) {
                        $used = $consumedShares->get($buyTx->id, 0);
                        $available = $buyTx->shares - $used;

                        if ($available <= 0) continue;

                        $matched = min($available, $sharesToSell);
                        $cost = $matched * $buyTx->price;
                        $proceeds = $matched * $transaction->price;

                        $gainLossInDollar += $proceeds - $cost;
                        $totalCostBasis += $cost;

                        $consumedShares->put($buyTx->id, $used + $matched);
                        $sharesToSell -= $matched;

                        if ($sharesToSell <= 0) break;
                    }

                    $transaction->gain_loss_in_dollar = round($gainLossInDollar, 2);
                    $transaction->gain_loss_in_percentage = $totalCostBasis > 0
                        ? round(($gainLossInDollar / $totalCostBasis) * 100, 2)
                        : null;

                    // ⚠️ Subtract cost from current cost basis
                    $currentCostBasis -= $totalCostBasis;
                }

                $transaction->current_shares_at_this_point_in_time = $currentSharesOwned;

                $transaction->save();
            }

            // Insert or Update transaction_metrics
            TransactionMetric::updateOrCreate([
                'portfolio_id' => $portfolioId,
                'security_id'  => $security->id,
            ], [
                'current_shares_owned' => $currentSharesOwned,
                'current_cost_basis'   => round($currentCostBasis, 2) * -1,
            ]);

            // Update Dividends Received
            $existingDividendsReceived = DividendReceived::query()
                ->where('portfolio_id', $portfolioId)
                ->where('security_id', $security->id)
                ->pluck('id');

            // ------------------------------------------------------------------------------------------------------------------------------------
            CashMovement::whereIn('dividend_received_id', $existingDividendsReceived)->delete(); // Delete first, we'll regenerate them immediately
            DividendReceived::whereIn('id', $existingDividendsReceived)->delete(); // Delete first, we'll regenerate them immediately
            // ------------------------------------------------------------------------------------------------------------------------------------

            $firstTransactionEver = Carbon::parse($transactions->first()->trade_date)->addDays(1); // Add 1 day since the settlement takes 1 day (T+1)
            $today = Carbon::now()->format('Y-m-d');

            $dividends = Dividend::query()
                ->where('security_id', $security->id)
                ->where('ex_dividend_date', '>=', $firstTransactionEver)
                ->where('payment_date', '<=', $today)
                ->orderBy('record_date')
                ->get();

            foreach ($dividends as $dividend) {
                $exDateTransaction = $transactions
                    ->where('trade_date', '<', $dividend->ex_dividend_date)
                    ->sortBy('trade_date')
                    ->reverse()
                    ->firstWhere('trade_date', '<', $dividend->ex_dividend_date);

                if ($exDateTransaction) {
                    $currentSharesAtThisPointInTime = $exDateTransaction->current_shares_at_this_point_in_time;

                    if ($currentSharesAtThisPointInTime > 0) {
                        $newDividendReceived = DividendReceived::create([
                            'portfolio_id'    => $portfolioId,
                            'security_id'     => $security->id,
                            'dividend_id'     => $dividend->id,
                            'shares'          => $currentSharesAtThisPointInTime,
                            'dividend_amount' => $dividend->amount,
                            'total'           => $currentSharesAtThisPointInTime * $dividend->amount,
                            'date'            => $dividend->payment_date,
                        ]);

//                        CashMovement::create([
//                            'portfolio_id'         => $portfolioId,
//                            'dividend_received_id' => $newDividendReceived->id,
//                            'type'                 => 'dividend',
//                            'amount'               => $currentSharesAtThisPointInTime * $dividend->amount,
//                            'description'          => "$security->ticker $security->name",
//                            'date'                 => $dividend->payment_date,
//                        ]);
                    }
                }
            }

        }

        // Update portfolio_metrics

        // Current Cash Available
        $currentCashAvailable = CashMovement::select('amount')->where('portfolio_id', $portfolioId)->sum('amount');

        PortfolioMetric::updateOrCreate([
            'portfolio_id' => $portfolioId,
        ], [
            'current_cash_available' => $currentCashAvailable,
        ]);
    }

    public function importHoldingsFromSchwab($portfolioId, UploadedFile $csvFile)
    {
        $csvPath = $csvFile->getRealPath();

        if (!file_exists($csvPath) || !is_readable($csvPath)) {
            throw new \Exception("CSV file is missing or unreadable.");
        }

        DB::beginTransaction();

        try {
            $this->wipe($portfolioId);

            $handle = fopen($csvPath, 'r');
            $header = fgetcsv($handle); // First row = column headers

            $rows = [];
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = array_combine($header, $row);
            }

            fclose($handle);

            // Process oldest to newest
            $currentTrueOrder = 1;

            foreach (array_reverse($rows) as $record) {
                // === Date handling (Schwab-specific quirks) ===
                $rawDate = trim($record['Date'] ?? '');
                preg_match_all('/\d{2}\/\d{2}\/\d{4}/', $rawDate, $matches);
                $date = isset($matches[0]) && count($matches[0]) > 0
                    ? Carbon::createFromFormat('m/d/Y', end($matches[0]))->toDateString()
                    : null;

                if (!$date) {
                    continue; // Skip rows without a valid date
                }

                // === Parse & normalize fields ===
                $normalizeMoney = fn($value) => (float)str_replace(['$', ','], '', $value ?: 0);

                $action = strtolower(trim($record['Action'] ?? ''));
                $symbol = strtoupper(trim($record['Symbol'] ?? ''));
                $description = trim($record['Description'] ?? '');
                $quantity = isset($record['Quantity']) ? (float)str_replace(',', '', $record['Quantity']) : 0;
                $price = $normalizeMoney($record['Price'] ?? 0);
                $fees = $normalizeMoney($record['Fees & Comm'] ?? 0);
                $amount = $normalizeMoney($record['Amount'] ?? 0);

                if (in_array($action, ['buy', 'reinvest shares'])) {
                    $amount = -abs($amount);
                }

                $security = $symbol ? Security::where('ticker', $symbol)->first() : null;
                $transactionId = null;

                // === Transactions ===
                if (in_array($action, ['buy', 'sell', 'reinvest shares'])) {
                    $transactionType = $action === 'reinvest shares' ? 'buy_drip' : $action;

                    $total = ($quantity * $price) + $fees;

                    if (in_array($transactionType, ['buy', 'buy_drip'])) {
                        $total *= -1;
                    }

                    $transaction = Transaction::create([
                        'uid'                                  => Str::uuid()->toString(),
                        'portfolio_id'                         => $portfolioId,
                        'security_id'                          => $security?->id,
                        'true_order'                           => $currentTrueOrder,
                        'type'                                 => $transactionType,
                        'shares'                               => $quantity,
                        'price'                                => $price,
                        'fees_and_commissions'                 => $fees,
                        'total'                                => $total,
                        'trade_value'                          => $quantity * $price,
                        'trade_date'                           => $date,
                        'current_shares_at_this_point_in_time' => 0, // the recalculate method will take care of this
                        'is_imported'                          => true
                    ]);

                    $transactionId = $transaction->id;

                    CashMovement::create([
                        'uid'            => Str::uuid()->toString(),
                        'portfolio_id'   => $portfolioId,
                        'transaction_id' => $transactionId,
                        'type'           => CashMovementHelper::matchType($transactionType),
                        'amount'         => $amount,
                        'description'    => $description,
                        'date'           => $date,
                        'is_imported'    => true,
                    ]);
                } // === Standalone cash movement ===
                else {
                    CashMovement::create([
                        'uid'            => Str::uuid()->toString(),
                        'portfolio_id'   => $portfolioId,
                        'transaction_id' => null,
                        'type'           => CashMovementHelper::matchType($action),
                        'amount'         => $amount,
                        'description'    => $description,
                        'date'           => $date,
                        'is_imported'    => true,
                    ]);
                }

                $currentTrueOrder++;
            }

            $this->addIsImportOnlyFlag($portfolioId);

            app(PortfolioService::class)->recalculate($portfolioId);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUpcomingDividends($portfolioId)
    {
        $transactionMetrics = TransactionMetric::query()
            ->where('portfolio_id', $portfolioId)
            ->where('current_shares_owned', '>', 0)
            ->get();

        $securityIdsInThisPortfolio = $transactionMetrics->pluck('security_id');

        $securities = Security::whereIn('id', $securityIdsInThisPortfolio)->get()->keyBy('id');

        $now = now()->toDateString();
        $limitDate = now()->addMonths(3)->toDateString();

        $dividends = Dividend::query()
            ->whereIn('security_id', $securityIdsInThisPortfolio)
            ->whereBetween('payment_date', [$now, $limitDate])
            ->orderBy('payment_date', 'asc')
            ->get();

        // Market Quotes used later
        $marketQuotes = MarketQuote::query()
            ->select('security_id', 'date', 'close')
            ->whereIn('security_id', $securityIdsInThisPortfolio)
            ->whereIn('date', $dividends->pluck('payment_date')->unique())
            ->get()
            ->keyBy(fn($quote) => $quote->security_id . '|' . $quote->date);

        // Secondary Currency
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        $data = [];
        foreach ($dividends as $dividend) {
            // Ex-Dividend Date
            $exDividendDate = $dividend->ex_dividend_date;

            // Payment Date
            $paymentDate = $dividend->payment_date;

            // Amount
            $amount = floatval($dividend->amount);
            $amountFormatted = NumberHelper::formatNumberToDollar($amount);

            // Currency
            $currency = $dividend->currency;

            // Current Shares Owned
            $currentSharesOwned = floatval($transactionMetrics->where('security_id', $dividend->security_id)->first()->current_shares_owned);
            $currentSharesOwnedFormatted = NumberHelper::formatNumberWithSeparators($currentSharesOwned, NumberHelper::countDecimals($currentSharesOwned));

            // Total Expected
            $totalExpected = $amount * $currentSharesOwned;
            $totalExpectedFormatted = NumberHelper::formatNumberToDollar($totalExpected);

            // Dividend Yield
            $quoteKey = $dividend->security_id . '|' . $dividend->payment_date;
            $marketQuoteAtTheTime = $marketQuotes[$quoteKey] ?? null;

            $dividendYield = $marketQuoteAtTheTime && $marketQuoteAtTheTime->close > 0
                ? ($amount / $marketQuoteAtTheTime->close) * 100
                : null;
            $dividendYieldFormatted = NumberHelper::formatNumberToPercentage($dividendYield);

            // Days Until Payment
            $daysUntilPayment = 0;
            $diff = now()->diffInDays(Carbon::parse($paymentDate), false);
            if ($diff >= 0) {
                $days = max(1, ceil($diff));
                $daysUntilPayment = $days;
            }

            $daysUntilPaymentFormatted = "{$daysUntilPayment}D";

            if ($daysUntilPayment === 0) {
                $daysUntilPaymentFormatted = 'Today';
            }

            // Security
            $security = $securities->get($dividend->security_id);
            $securityTicker = $security->ticker;
            $securityName = $security->name;
            $securityLogoUrl = $security->logo_url;

            $subData = [
                // Ex-Dividend Date
                'exDividendDate'              => $exDividendDate,

                // Payment Date
                'paymentDate'                 => $paymentDate,

                // Amount
                'amount'                      => $amount,
                'amountFormatted'             => $amountFormatted,

                // Currency
                'currency'                    => $currency,

                // Current Shares Owned
                'currentSharesOwned'          => $currentSharesOwned,
                'currentSharesOwnedFormatted' => $currentSharesOwnedFormatted,

                // Total Expected
                'totalExpected'               => $totalExpected,
                'totalExpectedFormatted'      => $totalExpectedFormatted,

                // Dividend Yield
                'dividendYield'               => $dividendYield,
                'dividendYieldFormatted'      => $dividendYieldFormatted,

                // Days Until Payment
                'daysUntilPayment'            => $daysUntilPayment,
                'daysUntilPaymentFormatted'   => $daysUntilPaymentFormatted,

                // Security
                'security'                    => [
                    'ticker'  => $securityTicker,
                    'name'    => $securityName,
                    'logoUrl' => $securityLogoUrl,
                ],
            ];

            // Secondary Currency
            if ($secondaryCurrency) {
                // Amount
                $amount = floatval($amount * $multiplier);
                $amountFormatted = NumberHelper::formatNumberToDollar($amount, $secondaryCurrency);

                // Total Expected
                $totalExpected = floatval($totalExpected * $multiplier);
                $totalExpectedFormatted = NumberHelper::formatNumberToDollar($totalExpected, $secondaryCurrency);

                $subData['secondaryCurrency'] = [

                    // Amount
                    'amount'                 => $amount,
                    'amountFormatted'        => $amountFormatted,

                    // Total Expected
                    'totalExpected'          => $totalExpected,
                    'totalExpectedFormatted' => $totalExpectedFormatted,
                ];
            }

            $data[] = $subData;
        }

        return $data;
    }

    public function store(array $portfolioData)
    {
        $user = auth()->user();

        $portfolio = Portfolio::create([
            'uid'                     => Str::uuid()->toString(),
            'user_id'                 => $user->id,
            'name'                    => $portfolioData['name'],
            'mode'                    => $portfolioData['mode'],
            'public_reference_code'   => PortfolioHelper::generatePublicReferenceCode(),
            'is_default'              => false,
            'is_public'               => false,
            'is_import_only'          => false,
            'is_included_in_calendar' => true
        ]);

        // Create Portfolio Metrics
        PortfolioMetric::create([
            'portfolio_id'           => $portfolio->id,
            'current_cash_available' => 0,
        ]);

        // Verify if user has no default portfolio
        $portfolios = Portfolio::query()
            ->where('user_id', $user->id)
            ->get();

        $hasDefaultPortfolio = $portfolios->where('is_default', true);

        if (!$hasDefaultPortfolio->count()) {
            $defaultPortfolio = $portfolios->first();
            $defaultPortfolio->update(['is_default' => true]);
        }

        return $portfolio;
    }

    public function update($portfolioId, array $portfolioData)
    {
        $user = auth()->user();

        $portfolio = Portfolio::find($portfolioId);
        $portfolio->name = $portfolioData['name'];
        $portfolio->is_public = $portfolioData['isPublic'] ?? false;
        $portfolio->save();

        // Handle Is Default
        if ($portfolioData['isDefault']) {
            // Reset all is_default from this user
            foreach (Portfolio::where('user_id', $user->id)->get() as $p) {
                $p->update(['is_default' => false]);
            }

            $portfolio->is_default = true;
            $portfolio->save();
        } else {
            // Verify if user has no default portfolio
            $this->setDefaultPortfolioIfNoneExists();
        }

        return $portfolio;
    }

    public function switchMode($portfolioId)
    {
        $portfolio = Portfolio::find($portfolioId);

        $this->wipe($portfolio->id);

        if ($portfolio->mode === 'simple') {
            $portfolio->mode = 'advanced';
        } else {
            $portfolio->mode = 'simple';
        }

        $portfolio->save();

        return $portfolio;
    }

    public function wipe($portfolioId)
    {
        $portfolio = Portfolio::find($portfolioId);

        // Delete Dividends Received
        DividendReceived::where('portfolio_id', $portfolio->id)->delete();

        // Delete Cash Movements
        CashMovement::where('portfolio_id', $portfolio->id)->delete();

        // Delete Transactions
        Transaction::where('portfolio_id', $portfolio->id)->delete();

        // Delete Transaction Metrics
        TransactionMetric::where('portfolio_id', $portfolio->id)->delete();

        // Delete Portfolio Metrics
        $portfolioMetric = PortfolioMetric::where('portfolio_id', $portfolio->id)->first();
        $portfolioMetric->current_cash_available = 0;
        $portfolioMetric->save();

        $this->removeIsImportOnlyFlag($portfolioId);

        return $portfolio;
    }

    public function delete($portfolioId)
    {
        $user = auth()->user();

        $portfolio = Portfolio::find($portfolioId);

        // Delete Dividends Received
        DividendReceived::where('portfolio_id', $portfolio->id)->delete();

        // Delete Cash Movements
        CashMovement::where('portfolio_id', $portfolio->id)->delete();

        // Delete Transactions
        Transaction::where('portfolio_id', $portfolio->id)->delete();

        // Delete Transaction Metrics
        TransactionMetric::where('portfolio_id', $portfolio->id)->delete();

        // Delete Portfolio Metrics
        PortfolioMetric::where('portfolio_id', $portfolio->id)->delete();

        // Delete Portfolio
        $portfolio->delete();

        // Verify if user has no default portfolio // TODO MOVE to a specific function
        $portfolios = Portfolio::where('user_id', $user->id)->get();

        if ($portfolios->count()) {
            $hasDefaultPortfolio = $portfolios->where('is_default', true);

            if (!$hasDefaultPortfolio->count()) {
                $defaultPortfolio = $portfolios->first();
                $defaultPortfolio->update(['is_default' => true]);
            }
        }

        return $portfolio;
    }

    public function updateHoldings($portfolioId, $holdings)
    {
        TransactionMetric::where('portfolio_id', $portfolioId)->delete();

        foreach ($holdings as $holding) {
            $security = Security::where('uid', $holding['security']['uid'])->first();
            $currentSharesOwned = $holding['currentSharesOwned'];
            $currentAveragePrice = $holding['averagePrice'];
            $currentCostBasis = $currentSharesOwned * $currentAveragePrice;

            if ($security) {
                TransactionMetric::updateOrCreate([
                    'portfolio_id' => $portfolioId,
                    'security_id'  => $security->id,
                ], [
                    'current_shares_owned'  => $currentSharesOwned,
                    'current_average_price' => $currentAveragePrice,
                    'current_cost_basis'    => round($currentCostBasis, 2) * -1,
                ]);
            }
        }

        $this->removeIsImportOnlyFlag($portfolioId);
    }

    public function getDividendsReceived($portfolioId)
    {
        $portfolio = Portfolio::find($portfolioId);

        if ($portfolio->mode === 'simple') {
            return [];
        }

        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        $dividendsReceived = DividendReceived::query()
            ->with('security', 'dividend')
            ->where('portfolio_id', $portfolioId)
            ->orderBy('date', 'desc')
            ->get();

        // Preload all relevant MarketQuotes in one query
        $marketQuotes = MarketQuote::query()
            ->select('security_id', 'date', 'close')
            ->whereIn('security_id', $dividendsReceived->pluck('security_id')->unique())
            ->whereIn('date', $dividendsReceived->pluck('date')->unique())
            ->get()
            ->keyBy(fn($quote) => $quote->security_id . '|' . $quote->date);

        $dividendsReceived = $dividendsReceived->map(function ($dividendReceived) use ($secondaryCurrency, $multiplier, $marketQuotes) {
            // Market Quote at the Time
            $quoteKey = $dividendReceived->security_id . '|' . $dividendReceived->date;
            $marketQuoteAtTheTime = $marketQuotes[$quoteKey] ?? null;

            // Shares
            $shares = floatval($dividendReceived->shares);
            $sharesFormatted = NumberHelper::formatNumberWithSeparators($shares, NumberHelper::countDecimals($shares));

            // Dividend Amount
            $dividendAmount = floatval($dividendReceived->dividend_amount);
            $dividendAmountFormatted = NumberHelper::formatNumberToDollar($dividendAmount);

            // Dividend Yield
            $dividendYield = $marketQuoteAtTheTime && $marketQuoteAtTheTime->close > 0
                ? ($dividendAmount / $marketQuoteAtTheTime->close) * 100
                : null;
            $dividendYieldFormatted = NumberHelper::formatNumberToPercentage($dividendYield);

            // Total
            $total = floatval($dividendReceived->total);
            $totalFormatted = NumberHelper::formatNumberToDollar($total);

            // Date
            $date = $dividendReceived->date;

            // Security
            $security = $dividendReceived->security;
            $securityTicker = $security->ticker;
            $securityName = $security->name;
            $securityLogoUrl = $security->logo_url;

            // Dividend
            $dividend = $dividendReceived->dividend;
            $recordDate = $dividend->record_date;
            $exDate = $dividend->ex_dividend_date;
            $paymentDate = $dividend->payment_date;

            // Secondary Currency
            $data = [
                // Shares
                'shares'                  => $shares,
                'sharesFormatted'         => $sharesFormatted,

                // Dividend Amount
                'dividendAmount'          => $dividendAmount,
                'dividendAmountFormatted' => $dividendAmountFormatted,

                // Dividend Yield
                'dividendYield'           => $dividendYield,
                'dividendYieldFormatted'  => $dividendYieldFormatted,

                // Total
                'total'                   => $total,
                'totalFormatted'          => $totalFormatted,

                // Date
                'date'                    => $date,

                // Security
                'security'                => [
                    'ticker'  => $securityTicker,
                    'name'    => $securityName,
                    'logoUrl' => $securityLogoUrl,
                ],

                // Dividend
                'dividend'                => [
                    'recordDate'  => $recordDate,
                    'exDate'      => $exDate,
                    'paymentDate' => $paymentDate,
                ],
            ];

            if ($secondaryCurrency) {

                // Dividend Amount
                $dividendAmount = floatval($dividendReceived->dividend_amount * $multiplier);
                $dividendAmountFormatted = NumberHelper::formatNumberToDollar($dividendReceived->dividend_amount * $multiplier, $secondaryCurrency);

                // Total
                $total = floatval($dividendReceived->total * $multiplier);
                $totalFormatted = NumberHelper::formatNumberToDollar($dividendReceived->total * $multiplier, $secondaryCurrency);

                $data['secondaryCurrency'] = [
                    // Dividend Amount
                    'dividendAmount'          => $dividendAmount,
                    'dividendAmountFormatted' => $dividendAmountFormatted,

                    // Total
                    'total'                   => $total,
                    'totalFormatted'          => $totalFormatted
                ];
            }

            return $data;
        });

        // Stats
        $totalPayments = count($dividendsReceived);
        $totalPaymentsFormatted = NumberHelper::formatNumberWithSeparators($totalPayments, 0);
        $totalReceived = $dividendsReceived->sum('total');
        $totalReceivedFormatted = NumberHelper::formatNumberToDollar($totalReceived);

        $stats = [
            'totalPayments'          => $totalPayments,
            'totalPaymentsFormatted' => $totalPaymentsFormatted,
            'totalReceived'          => $totalReceived,
            'totalReceivedFormatted' => $totalReceivedFormatted,
            ...($secondaryCurrency ? [
                'secondaryCurrency' => [
                    'totalReceived'          => round($totalReceived * $multiplier, 2),
                    'totalReceivedFormatted' => NumberHelper::formatNumberToDollar($totalReceived * $multiplier, $secondaryCurrency),
                ]
            ] : []),
        ];

        $yearsWithData = $dividendsReceived
            ->pluck('date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->year)
            ->unique()
            ->sort()
            ->reverse()
            ->values()
            ->all();

        $securitiesWithData = $dividendsReceived
            ->pluck('security.ticker')
            ->unique()
            ->values()
            ->all();

        // Totals
        $totals = $dividendsReceived
            ->groupBy(fn($item) => $item['security']['ticker'])
            ->map(function ($items, $ticker) use ($secondaryCurrency, $multiplier) {
                $total = $items->sum('total');

                return [
                    'ticker'            => $ticker,
                    'name'              => $items->first()['security']['name'],
                    'logoUrl'           => $items->first()['security']['logoUrl'],
                    'payments'          => count($items),
                    'paymentsFormatted' => NumberHelper::formatNumberWithSeparators(count($items), 0),
                    'total'             => $total,
                    'totalFormatted'    => NumberHelper::formatNumberToDollar($total),
                    ...($secondaryCurrency ? [
                        'secondaryCurrency' => [
                            'total'          => $total * $multiplier,
                            'totalFormatted' => NumberHelper::formatNumberToDollar($total * $multiplier, $secondaryCurrency),
                        ]
                    ] : []),
                ];
            })
            ->values();

        return [
            'stats'              => $stats,
            'yearsWithData'      => $yearsWithData,
            'securitiesWithData' => $securitiesWithData,
            'data'               => $dividendsReceived,
            'totals'             => $totals,
        ];
    }

    public function getCashMovements($portfolioId)
    {
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        $cashMovements = CashMovement::query()
            ->where('portfolio_id', $portfolioId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($cashMovement) use ($secondaryCurrency, $multiplier) {
                // UID
                $uid = $cashMovement->uid;
                // Type
                $type = $cashMovement->type;
                $typeFormatted = $cashMovement->typeFormatted;

                // Amount
                $amount = $cashMovement->amount;
                $amountFormatted = NumberHelper::formatNumberToDollar($amount);

                // Description
                $description = $cashMovement->description;

                // Note
                $note = $cashMovement->note;

                // Is Imported
                $isImported = $cashMovement->is_imported;

                // Date
                $date = $cashMovement->date;
                $dateFormatted = Carbon::parse($date)->format('F j, Y');

                $data = [
                    'uid'             => $uid,
                    'type'            => $type,
                    'typeFormatted'   => $typeFormatted,
                    'amount'          => $amount,
                    'amountFormatted' => $amountFormatted,
                    'description'     => $description,
                    'note'            => $note,
                    'isImported'      => $isImported,
                    'date'            => $date,
                    'dateFormatted'   => $dateFormatted,
                ];

                if ($secondaryCurrency) {
                    // Amount
                    $amount = $cashMovement->amount * $multiplier;
                    $amountFormatted = NumberHelper::formatNumberToDollar($cashMovement->amount * $multiplier, $secondaryCurrency);

                    $data['secondaryCurrency'] = [
                        'amount'          => $amount,
                        'amountFormatted' => $amountFormatted,
                    ];
                }

                return $data;
            });

        $typesWithData = collect(
            [
                'Deposit',
                'Withdrawal',
                'Security Buy',
                'Security Buy (DRIP)',
                'Security Sell',
                'Dividend',
                'Interest',
                'Other',
                'Unknown',
            ]
        )
            ->filter(function ($type) use ($cashMovements) {
                return $cashMovements->pluck('typeFormatted')->contains($type);
            })
            ->values()
            ->all();

        $yearsWithData = $cashMovements
            ->pluck('date')
            ->map(fn($date) => \Carbon\Carbon::parse($date)->year)
            ->unique()
            ->sort()
            ->reverse()
            ->values()
            ->all();

        return [
            'yearsWithData' => $yearsWithData,
            'typesWithData' => $typesWithData,
            'data'          => $cashMovements,
        ];
    }

    public function addIsImportOnlyFlag($portfolioId)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $portfolio->is_import_only = true;
        $portfolio->save();
    }

    public function removeIsImportOnlyFlag($portfolioId)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $portfolio->is_import_only = false;
        $portfolio->save();
    }

    public function getSecondaryCurrency()
    {
        $user = auth()->user();

        if ($user->isPremium) {
            return $user?->preferences->secondaryCurrency;
        }

        return null;
    }
}






























