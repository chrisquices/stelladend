<?php

namespace App\Console\Commands;

use App\Models\LatestMarketQuote;
use App\Models\MarketDate;
use App\Models\MarketQuote;
use App\Models\Security;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchLatestMarketDate extends Command
{
    protected $signature = 'app:fetch-latest-market-date';

    protected $description = 'Fetch latest market date';

    public function handle()
    {
        $this->info("Fetching latest market date...");

        $apiKey = config('polygon.api_key');

        $url = "https://api.polygon.io/v1/marketstatus/now?apiKey={$apiKey}";

        $response = Http::get($url);

        if (!$response->successful()) {
            $this->error("Polygon API request failed.");
            return;
        }

        $response = $response->json();
        $isMarketOpen = $response['market'] === 'open' || $response['market'] === 'extended-hours';

        if ($isMarketOpen) {
            $date = Carbon::parse($response['serverTime'])->format('Y-m-d');

            MarketDate::firstOrCreate([
                'date' => $date,
            ]);
        }
    }
}
