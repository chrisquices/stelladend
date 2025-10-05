<?php

namespace Database\Seeders;

use App\Models\MarketQuote;
use App\Models\Security;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MarketQuoteSeeder extends Seeder
{
    public function run(): void
    {
        $this->storeMarketQuotesFromJson('AAPL');
        $this->storeMarketQuotesFromJson('SCHD');
        $this->storeMarketQuotesFromJson('AVGO');
        $this->storeMarketQuotesFromJson('JEPI');
    }

    protected function storeMarketQuotesFromJson(string $ticker): void
    {
        $path = database_path("data/securities/" . strtolower($ticker) . ".json");
        $quotes = collect(json_decode(File::get($path), true));

        $security = Security::where('ticker', $ticker)->first();
        $source = 'stockanalysis';

        foreach ($quotes as $quote) {
            $close = (float) $quote['close'];

            $changeInPercentage = (float) str_replace('%', '', $quote['change']);
            $previousClose = $close / (1 + ($changeInPercentage / 100));
            $changeInDollar = $close - $previousClose;

            MarketQuote::create([
                'security_id'          => $security->id,
                'open'                 => $quote['open'],
                'high'                 => $quote['high'],
                'low'                  => $quote['low'],
                'close'                => $close,
                'volume'               => $quote['volume'],
                'change_in_dollar'     => $changeInDollar,
                'change_in_percentage' => $changeInPercentage,
                'source'               => $source,
                'date'                 => $quote['date'],
            ]);
        }
    }
}
