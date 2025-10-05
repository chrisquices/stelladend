<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Currency;
use App\Models\FxRate;
use App\Models\MarketDate;
use App\Services\AccountService;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class FetchLatestFxRates extends Command
{
    protected $signature = 'app:fetch-latest-fx-rates';

    protected $description = "Fetch latest FX rates";

    public function handle()
    {
        $this->info("Fetching latest FX rates...");

        $currencies = Currency::all()->keyBy('code');
        $latestMarketDates = MarketDate::latest('date')->take(5)->pluck('date');
        $dates = $latestMarketDates;

        foreach ($dates as $date) {
            $apiKey = config('polygon.api_key');
            $url = "https://api.polygon.io/v2/aggs/grouped/locale/global/market/fx/$date?adjusted=true&apiKey={$apiKey}";
            $response = Http::get($url);
            $fxRates = $response->json()['results'] ?? [];

            foreach ($fxRates as $fxRate) {
                if (!isset($fxRate['T'], $fxRate['vw']) || !$fxRate['vw']) {
                    continue;
                }

                $pair = str_replace('C:', '', $fxRate['T']);
                $fromCode = substr($pair, 0, 3);
                $toCode = substr($pair, 3, 3);

                // Only use direct USD→XXX pairs — ignore all others
                if ($fromCode !== 'USD') {
                    continue;
                }

                $fromCurrency = $currencies[$fromCode] ?? null;
                $toCurrency = $currencies[$toCode] ?? null;
                $rate = $fxRate['vw'];

                if (!$fromCurrency || !$toCurrency) {
                    continue;
                }

                FxRate::updateOrCreate(
                    [
                        'from_currency_id' => $fromCurrency->id,
                        'to_currency_id'   => $toCurrency->id,
                        'date'             => $date,
                    ],
                    [
                        'rate' => $rate,
                    ]
                );
            }
        }
    }
}
