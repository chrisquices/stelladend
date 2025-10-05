<?php

namespace App\Console\Commands;

use App\Models\Dividend;
use App\Models\LatestMarketQuote;
use App\Models\MarketDate;
use App\Models\MarketQuote;
use App\Models\Security;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class FetchLatestMarketQuotes extends Command
{
    protected $signature = 'app:fetch-latest-market-quotes {--date=}';

    protected $description = "Fetch latest market quotes";

    public function handle()
    {
        $this->info("Fetching latest market quotes...");

        $apiKey = config('polygon.api_key');

        $date = $this->option('date');

        if ($date) {
            $latestMarketDate = $date;
        } else {
            //        $latestMarketDate = MarketDate::latest('date')->value('date');
            $secondLatestMarketDate = MarketDate::orderBy('date', 'desc')->value('date');
            $latestMarketDate = $secondLatestMarketDate;
        }

        $url = "https://api.polygon.io/v2/aggs/grouped/locale/us/market/stocks/{$latestMarketDate}?adjusted=true&apiKey={$apiKey}";
        $response = Http::get($url);

        if (!$response->successful()) {
            $this->error("Polygon API request failed.");
            return;
        }

        $marketQuotes = $response->json('results', []);
        $marketQuotes = collect($marketQuotes)->keyBy('T');

        // Extract tickers from today's API response
        $securities = Security::all()->keyBy('ticker');

        $updatedMarketQuotes = [];

        // Loop over polygon results
        foreach ($securities as $security) {
            $marketQuote = $marketQuotes[$security['ticker']] ?? null;

            if (!$marketQuote) {
                continue;
            }

            // Ensure essential OHLC values exist and are valid
            if (!$marketQuote['t'] || !isset($marketQuote['o'], $marketQuote['h'], $marketQuote['l'], $marketQuote['c']) || $marketQuote['c'] <= 0) {
                $this->warn("Skipping invalid OHLC data for {$marketQuote['T']}");
                continue;
            }

            // Security
            $ticker = $marketQuote['T'];

            if (!isset($securities[$ticker])) {
                continue; // Skip this quote if the security isn't found
            }

            $security = $securities[$ticker];

            // Calculate Change in Dollar and Percentage
            $changeInDollar = null;
            $changeInPercentage = null;

            $previousClose = MarketQuote::query()
                ->where('security_id', $security->id)
                ->where('date', '<', $latestMarketDate)
                ->latest('date')
                ->value('close');

            if ($previousClose !== null && $previousClose != 0) {
                $close = $marketQuote['c'];
                $changeInDollar = $close - $previousClose;
                $changeInPercentage = ($changeInDollar / $previousClose) * 100;
            }

            // Create market quotes
            $updatedMarketQuotes[] = MarketQuote::updateOrCreate(
                ['security_id' => $security->id, 'date' => $latestMarketDate],
                [
                    'open'                 => $marketQuote['o'] ?? null,
                    'high'                 => $marketQuote['h'] ?? null,
                    'low'                  => $marketQuote['l'] ?? null,
                    'close'                => $marketQuote['c'] ?? null,
                    'volume'               => $marketQuote['v'] ?? null,
                    'trades'               => $marketQuote['n'] ?? null,
                    'change_in_dollar'     => $changeInDollar,
                    'change_in_percentage' => $changeInPercentage,
                    'source'               => 'polygon',
                ]
            );
        }

        // Update Latest Market Quotes
        foreach ($updatedMarketQuotes as $updateMarketQuote) {
            LatestMarketQuote::updateOrCreate(
                ['security_id' => $updateMarketQuote->security_id],
                [
                    'open'                 => $updateMarketQuote->open,
                    'high'                 => $updateMarketQuote->high,
                    'low'                  => $updateMarketQuote->low,
                    'close'                => $updateMarketQuote->close,
                    'volume'               => $updateMarketQuote->volume,
                    'trades'               => $updateMarketQuote->trades,
                    'change_in_dollar'     => $updateMarketQuote->change_in_dollar,
                    'change_in_percentage' => $updateMarketQuote->change_in_percentage,
                    'date'                 => $updateMarketQuote->date,
                ]
            );
        }
    }

    //
    //    DEPRECATED - Scrapes data from stockanalysis.com and stores latest_market_quote and market_quote data. A bit unreliable. Code is outdated.
    //
    //
    //    public function handle()
    //    {
    //        $securityIds = TransactionMetric::select('security_id')->get()->pluck('security_id')->toArray();
    //        $securities = Security::find($securityIds);
    //
    //        foreach ($securities as $security) {
    //            $ticker = $security->ticker;
    //
    //            if ($security->type === 'CS') $url = "https://stockanalysis.com/stocks/{$ticker}/history/";
    //            if ($security->type === 'ETF') $url = "https://stockanalysis.com/etf/{$ticker}/history/";
    //            if ($security->type === 'ETV') $url = "https://stockanalysis.com/etf/{$ticker}/history/";
    //
    //            try {
    //                $response = Http::withoutVerifying()
    //                    ->withHeaders([
    //                        'User-Agent' => 'Mozilla/5.0',
    //                        'Accept'     => 'text/html',
    //                    ])
    //                    ->get($url);
    //
    //                if ($response->failed()) {
    //                    $this->error("Failed to fetch data for {$ticker}");
    //                    continue;
    //                }
    //
    //                $html = $response->body();
    //
    //                // Scrape data from Historical Data table
    //                if (preg_match('/<table[^>]*>(.*?)<\/table>/s', $html, $tableMatch)) {
    //                    if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $tableMatch[1], $rows)) {
    //                        $firstRow = $rows[1][1] ?? null;
    //
    //                        if ($firstRow && preg_match_all('/<td[^>]*>(.*?)<\/td>/s', $firstRow, $cells)) {
    //                            $columns = array_map(
    //                                fn($cell) => trim(strip_tags($cell)),
    //                                $cells[1]
    //                            );
    //
    //                            if (count($columns) >= 8) {
    //                                [$date, $open, $high, $low, $close] = $columns;
    //                                $volume = (int)str_replace(',', '', $columns[7]);
    //                                $parsedDate = Carbon::parse($date)->format('Y-m-d');
    //
    //                                $this->info("{$ticker} | $parsedDate | O: $open | H: $high | L: $low | C: $close | V: $volume");
    //
    //                                $changeInPercentage = (float)str_replace('%', '', $columns[6]);
    //                                $previousClose = $close / (1 + ($changeInPercentage / 100));
    //                                $changeInDollar = $close - $previousClose;
    //
    //                                MarketQuote::updateOrCreate(
    //                                    [
    //                                        'security_id' => $security->id,
    //                                        'date'        => $parsedDate,
    //                                    ],
    //                                    [
    //                                        'open'                 => (float)$open,
    //                                        'high'                 => (float)$high,
    //                                        'low'                  => (float)$low,
    //                                        'close'                => (float)$close,
    //                                        'volume'               => $volume,
    //                                        'change_in_dollar'     => $changeInDollar,
    //                                        'change_in_percentage' => $changeInPercentage,
    //                                        'source'               => 'stockanalysis_scraper'
    //                                    ]
    //                                );
    //
    //                                MarketDate::updateOrCreate(
    //                                    [
    //                                        'date' => $parsedDate,
    //                                    ],
    //                                    [
    //                                    ]
    //                                );
    //
    //                                LatestMarketQuote::updateOrCreate(
    //                                    [
    //                                        'security_id' => $security->id,
    //                                    ],
    //                                    [
    //                                        'open'                 => (float)$open,
    //                                        'high'                 => (float)$high,
    //                                        'low'                  => (float)$low,
    //                                        'close'                => (float)$close,
    //                                        'volume'               => $volume,
    //                                        'change_in_dollar'     => $changeInDollar,
    //                                        'change_in_percentage' => $changeInPercentage,
    //                                        'date'                 => $parsedDate,
    //                                    ]
    //                                );
    //
    //                            } else {
    //                                $this->warn("Not enough columns for {$ticker}");
    //                            }
    //                        } else {
    //                            $this->warn("Could not extract data cells for {$ticker}");
    //                        }
    //                    }
    //                } else {
    //                    $this->warn("No table found for {$ticker}");
    //                }
    //
    //                // For additional precision, scrape data from the heading which includes Current Price, Change $ and Change %
    //                if (
    //                    preg_match('/<div class="text-4xl font-bold[^>]*>(.*?)<\/div>/s', $html, $priceMatch) &&
    //                    preg_match('/<div class="font-semibold[^>]*>(.*?)<\/div>/s', $html, $changeMatch)
    //                ) {
    //                    $price = floatval(trim(strip_tags($priceMatch[1])));
    //
    //                    // Example match: +0.14 (0.25%)
    //                    $changeText = trim(strip_tags($changeMatch[1]));
    //                    if (preg_match('/([+-]?[0-9.,]+)\s*\(([+-]?[0-9.,]+)%\)/', $changeText, $matches)) {
    //                        $changeInDollar = floatval(str_replace(',', '', $matches[1]));
    //                        $changeInPercentage = floatval(str_replace(',', '', $matches[2]));
    //
    //                        // Extract the date from real-time metadata
    //                        $realTimeDate = null;
    //                        if (preg_match('/<div class="mt-0\.5 text-xxs text-faded[^"]*">.*?(?:At close:)?\s*(.*?)<\/div>/s', $html, $dateMatch)) {
    //                            $dateText = strip_tags($dateMatch[1]);
    //                            if (preg_match('/([A-Za-z]{3} \d{1,2}, \d{4})/', $dateText, $parsedDateMatch)) {
    //                                $realTimeDate = \Carbon\Carbon::parse($parsedDateMatch[1])->format('Y-m-d');
    //                            } else {
    //                                $this->warn("Could not parse real-time date format: $dateText");
    //                            }
    //                        } else {
    //                            $this->warn("Missing real-time date line for {$ticker}");
    //                        }
    //
    //                        if ($realTimeDate) {
    //                            $this->info("REAL-TIME: {$ticker} | Price: {$price} | Δ$ {$changeInDollar} | Δ% {$changeInPercentage} | Date: {$realTimeDate}");
    //
    ////                            MarketQuote::updateOrCreate(
    ////                                [
    ////                                    'security_id' => $security->id,
    ////                                    'date'        => $realTimeDate,
    ////                                ],
    ////                                [
    ////                                    'close'                => $price,
    ////                                    'change_in_dollar'     => $changeInDollar,
    ////                                    'change_in_percentage' => $changeInPercentage,
    ////                                    'source'               => 'stockanalysis_scraper',
    ////                                ]
    ////                            );
    //
    //                            LatestMarketQuote::updateOrCreate(
    //                                [
    //                                    'security_id' => $security->id,
    //                                ],
    //                                [
    //                                    'close'                => $price,
    //                                    'change_in_dollar'     => $changeInDollar,
    //                                    'change_in_percentage' => $changeInPercentage,
    //                                    'date'                 => $realTimeDate,
    //                                    'source'               => 'stockanalysis_scraper',
    //                                ]
    //                            );
    //                        } else {
    //                            $this->warn("Skipping real-time update for {$ticker} due to missing date.");
    //                        }
    //                    } else {
    //                        $this->warn("Could not parse change format for {$ticker}");
    //                    }
    //                } else {
    //                    $this->warn("Could not extract real-time price data for {$ticker}");
    //                }
    //
    //            } catch (\Exception $e) {
    //                $this->error("Error scraping {$ticker}: " . $e->getMessage());
    //            }
    //        }
    //    }
}
