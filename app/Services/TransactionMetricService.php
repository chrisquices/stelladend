<?php

namespace App\Services;

use App\Helpers\NumberHelper;
use App\Models\Dividend;
use App\Models\DividendReceived;
use App\Models\LatestMarketQuote;
use App\Models\MarketQuote;
use App\Models\Portfolio;
use App\Models\Security;
use App\Models\Transaction;
use App\Models\TransactionMetric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionMetricService
{
    public function deleteTransactionMetricsWithZeroTransactions()
    {
        $user = auth()->user();

        $portfolioIds = Portfolio::where('user_id', $user->id)->pluck('id');

        $transactionMetrics = TransactionMetric::whereIn('portfolio_id', $portfolioIds)->get();

        foreach ($transactionMetrics as $transactionMetric) {
            $hasTransactions = Transaction::where('portfolio_id', $transactionMetric->portfolio_id)
                ->where('security_id', $transactionMetric->security_id)
                ->exists();

            if (! $hasTransactions) {
                $transactionMetric->delete();
            }
        }
    }
}
