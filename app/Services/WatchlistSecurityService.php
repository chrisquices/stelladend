<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\CashMovement;
use App\Models\Dividend;
use App\Models\DividendReceived;
use App\Models\LatestMarketQuote;
use App\Models\MarketQuote;
use App\Models\Portfolio;
use App\Models\PortfolioMetric;
use App\Models\Transaction;
use App\Models\TransactionMetric;
use App\Models\Watchlist;
use App\Models\WatchlistSecurity;
use Carbon\Carbon;
use Illuminate\Support\Str;

class WatchlistSecurityService
{
    public function store($watchlistId, $securityId)
    {
        return WatchlistSecurity::create([
            'watchlist_id' => $watchlistId,
            'security_id'  => $securityId,
        ]);
    }

    public function delete($watchlistId, $securityId)
    {
        $watchlistSecurity = WatchlistSecurity::query()
            ->where('watchlist_id', $watchlistId)
            ->where('security_id', $securityId)
            ->first();

        $watchlistSecurity->delete();
    }

}
