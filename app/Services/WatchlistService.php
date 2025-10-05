<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\Dividend;
use App\Models\LatestMarketQuote;
use App\Models\Watchlist;
use App\Models\WatchlistSecurity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class WatchlistService
{
    public function storeStarterWatchlist()
    {
        $user = auth()->user();

        $watchlists = Watchlist::where('user_id', $user->id)->exists();

        if (!$watchlists) {
            Watchlist::create([
                'uid'                     => Str::uuid()->toString(),
                'user_id'                 => $user->id,
                'name'                    => 'Default Watchlist',
                'is_public'               => false,
                'is_default'              => true,
                'is_included_in_calendar' => true
            ]);
        }
    }

    public function getDefaultWatchlist()
    {
        $user = auth()->user();

        return Watchlist::query()
            ->where('user_id', $user->id)
            ->where('is_default', true)
            ->firstOrFail();
    }

    public function getWatchlists()
    {
        $user = auth()->user();

        $query = Watchlist::query()->where('user_id', $user->id);

        if (!$user->is_premium) {
            $query->where('is_default', true);
        }

        return $query->get()->map(function ($watchlist) {
            return [
                'uid'      => $watchlist->uid,
                'name'     => $watchlist->name,
                'isPublic' => $watchlist->is_public,
            ];
        });
    }

    public function getWatchlist($watchlistId, $sortBy = 'ticker', $sortDirection = 'asc')
    {
        // Eager-load watchlistSecurities and their associated securities
        $watchlist = Watchlist::with('watchlistSecurities.security')->findOrFail($watchlistId);

        // Extract security IDs
        $securityIdsInThisWatchlist = $watchlist->watchlistSecurities->pluck('security_id')->all();

        // Latest market quotes
        $latestMarketQuotes = LatestMarketQuote::query()
            ->whereIn('security_id', $securityIdsInThisWatchlist)
            ->get()
            ->keyBy('security_id');

        // Past 12 months dividends for yield calculation
        $dividends = Dividend::query()
            ->select('security_id', 'amount', 'ex_dividend_date', 'payment_date')
            ->whereIn('security_id', $securityIdsInThisWatchlist)
            ->where('payment_date', '>=', now()->subYear())
            ->orderBy('payment_date', 'desc')
            ->get()
            ->groupBy('security_id');

        // Upcoming dividends (for next ex-date and payment date)
        $upcomingDividends = Dividend::query()
            ->select('security_id', 'ex_dividend_date', 'payment_date')
            ->whereIn('security_id', $securityIdsInThisWatchlist)
            ->where('ex_dividend_date', '>=', now())
            ->orderBy('ex_dividend_date', 'asc')
            ->get()
            ->groupBy('security_id');

        // Secondary Currency
        $user = auth()->user();
        $secondaryCurrency = $user->preferences->secondaryCurrency;

        // Assemble watchlist data
        $watchlistSecurities = $watchlist->watchlistSecurities->map(function ($watchlistSecurity) use ($latestMarketQuotes, $dividends, $upcomingDividends, $secondaryCurrency) {
            $security = $watchlistSecurity->security;

            $securityId = $security->id;

//            $securityMarketQuotes = $marketQuotes[$securityId] ?? collect();
            $latestMarketQuote = $latestMarketQuotes[$securityId] ?? null;

            if (!$latestMarketQuote) {
                return null; // Skip if no data available
            }

            // Current Market Price
            $currentMarketPrice = floatval($latestMarketQuote->close);
            $currentMarketPriceFormatted = NumberHelper::formatNumberToDollar($currentMarketPrice);

            // Change in Dollar
            $currentChangeInDollar = floatval($latestMarketQuote->change_in_dollar);
            $currentChangeInDollarFormatted = NumberHelper::formatNumberToDollar($currentChangeInDollar);

            // Change in Percentage
            $currentChangeInPercentage = floatval($latestMarketQuote->change_in_percentage);
            $currentChangeInPercentageFormatted = NumberHelper::formatNumberToPercentage($currentChangeInPercentage);

            // Dividend Per Share
            $securityDividends = $dividends[$securityId] ?? collect();
            $currentDividendPerShare = $securityDividends->sum('amount');
            $currentDividendPerShareFormatted = NumberHelper::formatNumberToDollar($currentDividendPerShare);

            // Current Dividend Yield
            $currentDividendYield = $currentMarketPrice > 0
                ? ($currentDividendPerShare / $currentMarketPrice) * 100
                : 0;
            $currentDividendYieldFormatted = NumberHelper::formatNumberToPercentage($currentDividendYield, 2);

            // Payout Frequency
            $dividendPayoutFrequency = $security->dividend_payout_frequency;
            $dividendPayoutFrequencyFormatted = $security->dividend_payout_frequency_formatted;

            // Upcoming dividend data
            $nextDividend = optional($upcomingDividends[$securityId] ?? collect())->first();

            $nextExDividendDate = optional($nextDividend)->ex_dividend_date;
            $nextExDateParsed = $nextExDividendDate ? Carbon::parse($nextExDividendDate) : null;
            $daysUntilExDividendDate = $nextExDateParsed
                ? max(now()->startOfDay()->diffInDays($nextExDateParsed->startOfDay(), false), 0)
                : null;

            $nextPaymentDate = optional($nextDividend)->payment_date;
            $nextPaymentDateParsed = $nextPaymentDate ? Carbon::parse($nextPaymentDate) : null;
            $daysUntilPaymentDate = $nextPaymentDateParsed
                ? max(now()->startOfDay()->diffInDays($nextPaymentDateParsed->startOfDay(), false), 0)
                : null;

            $data = [
                // Security
                'uid'                                => $security->uid,
                'ticker'                             => $security->ticker,
                'name'                               => $security->name,
                'logoUrl'                            => $security->logoUrl,
                'color'                              => $security->color,

                // Current Market Price
                'currentMarketPrice'                 => $currentMarketPrice,
                'currentMarketPriceFormatted'        => $currentMarketPriceFormatted,

                // Change in Dollar
                'currentChangeInDollar'              => $currentChangeInDollar,
                'currentChangeInDollarFormatted'     => $currentChangeInDollarFormatted,

                // Change in Percentage
                'currentChangeInPercentage'          => $currentChangeInPercentage,
                'currentChangeInPercentageFormatted' => $currentChangeInPercentageFormatted,

                // Dividend Yield
                'currentDividendYield'               => $currentDividendYield,
                'currentDividendYieldFormatted'      => $currentDividendYieldFormatted,

                // Dividend per Share (annual)
                'currentDividendPerShare'            => $currentDividendPerShare,
                'currentDividendPerShareFormatted'   => $currentDividendPerShareFormatted,

                // Payout Frequency
                'dividendPayoutFrequency'            => $dividendPayoutFrequency,
                'dividendPayoutFrequencyFormatted'   => $dividendPayoutFrequencyFormatted,

                // Next ex-dividend date
                'nextExDividendDate'                 => $nextExDividendDate,

                // Days Until Ex Dividend Date
                'daysUntilExDividendDate'            => $daysUntilExDividendDate,

                // Next payment date
                'nextPaymentDate'                    => $nextPaymentDate,

                // Days until payment date
                'daysUntilPaymentDate'               => $daysUntilPaymentDate,
            ];

            // Secondary Currency
            $secondaryCurrency = auth()->user()->preferences->secondaryCurrency;
            $multiplier = $secondaryCurrency
                ? app(FxRateService::class)->getLatestFxRate(currencyId: $secondaryCurrency->id)->rate
                : null;

            if ($secondaryCurrency) {

                // Current Market Price
                $currentMarketPrice = floatval($latestMarketQuote->close * $multiplier);
                $currentMarketPriceFormatted = NumberHelper::formatNumberToDollar($currentMarketPrice, $secondaryCurrency);

                // Change in Dollar
                $currentChangeInDollar = floatval($latestMarketQuote->change_in_dollar * $multiplier);
                $currentChangeInDollarFormatted = NumberHelper::formatNumberToDollar($currentChangeInDollar, $secondaryCurrency);

                // Dividend Per Share
                $currentDividendPerShare = floatval($securityDividends->sum('amount') * $multiplier);
                $currentDividendPerShareFormatted = NumberHelper::formatNumberToDollar($currentDividendPerShare, $secondaryCurrency);

                $data['secondaryCurrency'] = [
                    // Current Market Price
                    'currentMarketPrice'               => $currentMarketPrice,
                    'currentMarketPriceFormatted'      => $currentMarketPriceFormatted,

                    // Change in Dollar
                    'currentChangeInDollar'            => $currentChangeInDollar,
                    'currentChangeInDollarFormatted'   => $currentChangeInDollarFormatted,

                    // Dividend per Share (annual)
                    'currentDividendPerShare'          => $currentDividendPerShare,
                    'currentDividendPerShareFormatted' => $currentDividendPerShareFormatted,
                ];
            }

            return $data;
        })
            ->filter()
            ->sortBy(function ($item) use ($sortBy) {
                return data_get($item, $sortBy);
            }, SORT_REGULAR, strtolower($sortDirection) === 'desc')
            ->values();

        return [
            'uid'                 => $watchlist->uid,
            'name'                => $watchlist->name,
            'isPublic'            => $watchlist->is_public,
            'watchlistSecurities' => $watchlistSecurities,
        ];
    }

    public function getWatchlistByUid($watchlistUid)
    {
        return Watchlist::query()
            ->where('uid', $watchlistUid)
            ->firstOrFail();
    }

    public function store(array $watchlistData)
    {
        $user = auth()->user();

        $watchlist = Watchlist::create([
            'uid'                     => Str::uuid()->toString(),
            'user_id'                 => $user->id,
            'name'                    => $watchlistData['name'],
            'is_public'               => false,
            'is_default'              => false,
            'is_included_in_calendar' => true
        ]);

        // Verify if user has no default watchlist
        $watchlists = Watchlist::query()
            ->where('user_id', $user->id)
            ->get();

        $hasDefaultWatchlist = $watchlists->where('is_default', true);

        if (!$hasDefaultWatchlist->count()) {
            $defaultWatchlist = $watchlists->first();
            $defaultWatchlist->update(['is_default' => true]);
        }

        return $watchlist;
    }

    public function update($watchlistId, array $watchlistData)
    {
        $user = auth()->user();

        $watchlist = Watchlist::find($watchlistId);
        $watchlist->name = $watchlistData['name'];
        $watchlist->save();

        // Verify if user has no default watchlist
        $watchlists = Watchlist::query()
            ->where('user_id', $user->id)
            ->get();

        $hasDefaultWatchlist = $watchlists->where('is_default', true);

        if (!$hasDefaultWatchlist->count()) {
            $defaultWatchlist = $watchlists->first();
            $defaultWatchlist->update(['is_default' => true]);
        }

        return $watchlist;
    }

    public function delete($watchlistId)
    {
        $user = auth()->user();

        $watchlist = Watchlist::find($watchlistId);

        // Watchlist Securities
        WatchlistSecurity::where('watchlist_id', $watchlist->id)->delete();

        // Delete Watchlist
        $watchlist->delete();

        // Verify if user has no default watchlist // TODO refactor
        $watchlists = Watchlist::query()
            ->where('user_id', $user->id)
            ->get();

        if ($watchlists->count()) {
            $hasDefaultWatchlist = $watchlists->where('is_default', true);

            if ($hasDefaultWatchlist->count() === 0) {
                $defaultWatchlist = $watchlists->first();
                $defaultWatchlist->update(['is_default' => true]);
            }
        }
    }


    public function export($watchlistId)
    {
        $watchlist = $this->getWatchlist($watchlistId);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . Str::slug($watchlist['name']) . '.csv"',
        ];

        $callback = function () use ($watchlist) {
            $watchlistSecurities = $watchlist['watchlistSecurities'];

            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['SecurityTicker', 'SecurityName', 'NextExDividendDate', 'NextPaymentDate', 'PayoutFrequency', 'DividendYield', 'DividendPerShare', 'CurrentPrice', 'AsOfDate']);

            foreach ($watchlistSecurities as $watchlistSecurity) {
                fputcsv($handle, [
                    $watchlistSecurity['ticker'],
                    $watchlistSecurity['name'],
                    $watchlistSecurity['nextExDividendDate'] ?? '',
                    $watchlistSecurity['nextPaymentDate'] ?? '',
                    $watchlistSecurity['dividendPayoutFrequencyFormatted'],
                    $watchlistSecurity['currentDividendYieldFormatted'],
                    $watchlistSecurity['currentDividendPerShareFormatted'],
                    $watchlistSecurity['currentMarketPriceFormatted'],
                    now()->toDateString(),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

}
