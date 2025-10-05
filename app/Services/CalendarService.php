<?php

namespace App\Services;

use App\Helpers\CalendarHelper;
use App\Helpers\NumberHelper;
use App\Models\Dividend;
use App\Models\Portfolio;
use App\Models\TransactionMetric;
use App\Models\Watchlist;
use App\Models\WatchlistSecurity;
use Carbon\Carbon;

class CalendarService
{
    public function getCalendarEvents($period)
    {
        $user = auth()->user();

        $portfolioIds = Portfolio::query()
            ->where('is_included_in_calendar', true)
            ->where('user_id', $user->id)
            ->pluck('id');

        $watchlistIds = Watchlist::query()
            ->where('is_included_in_calendar', true)
            ->where('user_id', $user->id)
            ->pluck('id');

        $securitiesInThesePortfolios = TransactionMetric::query()
            ->whereIn('portfolio_id', $portfolioIds)
            ->where('current_shares_owned', '>', 0)
            ->pluck('security_id');

        $securitiesInTheseWatchlists = WatchlistSecurity::query()
            ->whereIn('watchlist_id', $watchlistIds)
            ->pluck('security_id');

        $securityIds = $securitiesInThesePortfolios->merge($securitiesInTheseWatchlists);

        $minDate = Carbon::now()->startOfMonth()->subMonths(12);
        $maxDate = Carbon::now()->startOfMonth()->addMonths(12)->endOfMonth();

        $dividends = Dividend::query()
            ->with('security')
            ->select('security_id', 'ex_dividend_date', 'payment_date', 'amount', 'currency')
            ->whereIn('security_id', $securityIds)
            ->whereBetween('ex_dividend_date', [$minDate, $maxDate])
            ->get();

        $calendarDays = CalendarHelper::generateCalendarDays($period);

        $transactionMetrics = TransactionMetric::query()
            ->whereIn('portfolio_id', $portfolioIds)
            ->get();


        // FX Rates
        $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
        $multiplier = $secondaryCurrency
            ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
            : null;

        foreach ($calendarDays as &$calendarDay) {
            $events = collect();

            $exDividendDates = $dividends->where('ex_dividend_date', $calendarDay['date'])->values();
            $paymentDates = $dividends->where('payment_date', $calendarDay['date'])->values();

            // Process Ex-Dividend Dates
            foreach ($exDividendDates as $dividend) {

                // Security
                $security = $dividend->security;
                $ticker = $security->ticker ?? '';
                $name = $security->name ?? '';
                $logoUrl = $security->logoUrl ?? '';
                $color = $security->color ?? '';

                // Ex-Dividend Date
                $exDividendDate = $dividend->ex_dividend_date;

                // Payment Date
                $paymentDate = $dividend->payment_date;

                // Days Until Ex-Dividend Date
                $daysUntilExDividendDate = max(now()->startOfDay()->diffInDays(Carbon::parse($exDividendDate)->startOfDay(), false), 0);

                // Days Until Payment Date
                $daysUntilPaymentDate = max(now()->startOfDay()->diffInDays(Carbon::parse($paymentDate)->startOfDay(), false), 0);

                // Amount
                $amount = floatval($dividend->amount);
                $amountFormatted = NumberHelper::formatNumberToDollar($amount);

                // Currency
                $currency = $dividend->currency;

                // Current Shares Owned
                $currentSharesOwned = $transactionMetrics->where('security_id', $dividend->security_id)->sum('current_shares_owned');
                $currentSharesOwnedFormatted = NumberHelper::formatNumberWithSeparators($currentSharesOwned);

                // Total Expected
                $totalExpected = $amount * $currentSharesOwned;
                $totalExpectedFormatted = NumberHelper::formatNumberToDollar($totalExpected);

                $events->push([

                    // Security
                    'type'                        => 'ex',
                    'ticker'                      => $ticker,
                    'name'                        => $name,
                    'logoUrl'                     => $logoUrl,
                    'color'                       => $color,

                    // Ex-Dividend Date
                    'exDividendDate'              => $exDividendDate,

                    // Payment Date
                    'paymentDate'                 => $paymentDate,

                    // Days Until Ex-Dividend Date
                    'daysUntilExDividendDate'     => $daysUntilExDividendDate,

                    // Days Until Payment Date
                    'daysUntilPaymentDate'        => $daysUntilPaymentDate,

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
                ]);
            }

            // Process Payment Dates
            foreach ($paymentDates as $dividend) {

                // Security
                $security = $dividend->security;
                $ticker = $security->ticker ?? '';
                $name = $security->name ?? '';
                $logoUrl = $security->logoUrl ?? '';
                $color = $security->color ?? '';

                // Ex-Dividend Date
                $exDividendDate = $dividend->ex_dividend_date;

                // Payment Date
                $paymentDate = $dividend->payment_date;

                // Days Until Ex-Dividend Date
                $daysUntilExDividendDate = max(now()->startOfDay()->diffInDays(Carbon::parse($exDividendDate)->startOfDay(), false), 0);

                // Days Until Payment Date
                $daysUntilPaymentDate = max(now()->startOfDay()->diffInDays(Carbon::parse($paymentDate)->startOfDay(), false), 0);

                // Amount
                $amount = floatval($dividend->amount);
                $amountFormatted = NumberHelper::formatNumberToDollar($amount);

                // Currency
                $currency = $dividend->currency;

                // Current Shares Owned
                $currentSharesOwned = floatval(optional($transactionMetrics->where('security_id', $dividend->security_id)->first())->current_shares_owned);
                $currentSharesOwnedFormatted = NumberHelper::formatNumberWithSeparators($currentSharesOwned);

                // Total Expected
                $totalExpected = $amount * $currentSharesOwned;
                $totalExpectedFormatted = NumberHelper::formatNumberToDollar($totalExpected);

                $events->push([
                    // Security
                    'type'                        => 'pay',
                    'ticker'                      => $ticker,
                    'name'                        => $name,
                    'logoUrl'                     => $logoUrl,
                    'color'                       => $color,

                    // Ex-Dividend Date
                    'exDividendDate'              => $exDividendDate,

                    // Payment Date
                    'paymentDate'                 => $paymentDate,


                    // Days Until Ex-Dividend Date
                    'daysUntilExDividendDate'     => $daysUntilExDividendDate,

                    // Days Until Payment Date
                    'daysUntilPaymentDate'        => $daysUntilPaymentDate,

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
                ]);
            }

            // Events
            $events = $events->map(function ($event) use ($secondaryCurrency, $multiplier) {

                if ($secondaryCurrency) {

                    // Amount
                    $amount = $event['amount'] * $multiplier;
                    $amountFormatted = NumberHelper::formatNumberToDollar($amount, $secondaryCurrency);

                    // Total Expected
                    $totalExpected = floatval($event['amount'] * $event['currentSharesOwned']) * $multiplier;
                    $totalExpectedFormatted = NumberHelper::formatNumberToDollar($totalExpected, $secondaryCurrency);

                    $event['secondaryCurrency'] = [

                        // Amount
                        'amount'                 => $amount,
                        'amountFormatted'        => $amountFormatted,

                        // Total Expected
                        'totalExpected'          => $totalExpected,
                        'totalExpectedFormatted' => $totalExpectedFormatted,
                    ];
                }
                return $event;
            });

            $calendarDay['events'] = $events->all();
        }

        unset($calendarDay);

        return $calendarDays;
    }

    public function getPortfolios()
    {
        $user = auth()->user();

        return Portfolio::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get()
            ->map(function ($portfolio) {
                return [
                    'uid'                  => $portfolio->uid,
                    'name'                 => $portfolio->name,
                    'isIncludedInCalendar' => $portfolio->is_included_in_calendar,
                ];
            });
    }

    public function getWatchlists()
    {
        $user = auth()->user();

        return Watchlist::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->get()
            ->map(function ($watchlist) {
                return [
                    'uid'                  => $watchlist->uid,
                    'name'                 => $watchlist->name,
                    'isIncludedInCalendar' => $watchlist->is_included_in_calendar,
                ];
            });
    }

    public function togglePortfolioInclusion($portfolioId)
    {
        $portfolio = Portfolio::query()->where('id', $portfolioId)->first();

        $portfolio->update([
            'is_included_in_calendar' => !$portfolio->is_included_in_calendar,
        ]);
    }

    public function toggleWatchlistInclusion($watchlistId)
    {
        $watchlist = Watchlist::query()->where('id', $watchlistId)->first();

        $watchlist->update([
            'is_included_in_calendar' => !$watchlist->is_included_in_calendar,
        ]);
    }
}
