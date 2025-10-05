<?php

namespace App\Console\Commands;

use App\Models\Dividend;
use App\Models\Security;
use App\Models\TransactionMetric;
use App\Models\WatchlistSecurity;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class FetchLatestDividends extends Command
{
    protected $signature = 'app:fetch-latest-dividends';

    protected $description = "Fetch latest dividends";

    public function handle()
    {
        $this->info("Fetching latest dividends...");

        $securitiesGroup1 = TransactionMetric::all()->pluck('security_id');
        $securitiesGroup2 = WatchlistSecurity::all()->pluck('security_id');

        $securitiesWithActivity = Security::find($securitiesGroup1->merge($securitiesGroup2));

        foreach ($securitiesWithActivity as $security) {
            $apiKey = config('polygon.api_key');

            $url = "https://api.polygon.io/v3/reference/dividends?ticker=$security->ticker&order=asc&limit=1000&sort=ex_dividend_date&apiKey={$apiKey}";
            $response = Http::get($url);
            $dividends = $response->json()['results'];

            if (!$response->successful()) {
                $this->error("Polygon API request failed.");
                return;
            }

            foreach ($dividends as $dividend) {
                Dividend::updateOrCreate([
                    'security_id'      => $security->id,
                    'source_reference' => $dividend['id'],
                ], [
                    'ex_dividend_date' => $dividend['ex_dividend_date'],
                    'declaration_date' => $dividend['declaration_date'] ?? null,
                    'record_date'      => $dividend['record_date'] ?? null,
                    'payment_date'     => $dividend['pay_date'] ?? null,
                    'amount'           => $dividend['cash_amount'] ?? null,
                    'currency'         => $dividend['currency'] ?? null,
                    'source'           => 'polygon',
                ]);
            }

            // Set dividend payout frequency
            if (collect($dividends)->count() > 0) {
                $dividendPayoutFrequency = collect($dividends)->last()['frequency'];

                $security->dividend_payout_frequency = $dividendPayoutFrequency;
                $security->save();
            }

            $security->save();

            $dividendsCount = count($dividends);
            $this->info("Fetched $security->ticker... Stored $dividendsCount dividends");

            sleep(20);
        }
    }
}
