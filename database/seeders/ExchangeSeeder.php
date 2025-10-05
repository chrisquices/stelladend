<?php

namespace Database\Seeders;

use App\Models\Exchange;
use Illuminate\Database\Seeder;

class ExchangeSeeder extends Seeder
{
    public function run(): void
    {
        $exchanges = [
            // --- NASDAQ ---
            [
                'code'        => 'nasdaq',
                'name'        => 'NASDAQ',
                'mic'         => 'XNAS',
                'description' => 'An electronic exchange focused on technology and growth stocks, including Apple, Microsoft, and Amazon.'
            ],
            // --- NYSE Group ---
            [
                'code'        => 'nyse',
                'name'        => 'NYSE',
                'mic'         => 'XNYS',
                'description' => 'The largest U.S. stock exchange by market capitalization, listing major corporations like IBM, JPMorgan, and Coca-Cola.'
            ],
            [
                'code'        => 'amex',
                'name'        => 'NYSE American',
                'mic'         => 'XASE',
                'description' => 'An exchange for small-cap companies and ETFs, formerly known as the American Stock Exchange.'
            ],
            [
                'code'        => 'arca',
                'name'        => 'NYSE Arca',
                'mic'         => 'ARCX',
                'description' => 'A leading exchange for ETFs and ETPs, operated by the New York Stock Exchange.'
            ],
            // --- Cboe (BATS) Group ---
            [
                'code'        => 'bats',
                'name'        => 'BATS',
                'mic'         => 'BATS',
                'description' => 'An electronic exchange known for high-speed trading and competitive pricing.'
            ],
            [
                'code'        => 'bzx',
                'name'        => 'BZX',
                'mic'         => 'XBOS',
                'description' => 'Offers both equities and ETF trading; known for listing many popular ETFs.'
            ],
            [
                'code'        => 'byx',
                'name'        => 'BYX',
                'mic'         => 'BYXX',
                'description' => 'A Cboe-operated venue offering simple, low-latency equity trading.'
            ],
            [
                'code'        => 'edgx',
                'name'        => 'EDGX',
                'mic'         => 'EDGX',
                'description' => 'Part of Cboe Global Markets, offering U.S. equities trading with enhanced price discovery.'
            ],
            [
                'code'        => 'edga',
                'name'        => 'EDGA',
                'mic'         => 'EDGA',
                'description' => 'A lower-volume exchange focusing on cost-effective routing and order execution.'
            ],

            // --- Independent / Newer Exchanges ---
            [
                'code'        => 'iex',
                'name'        => 'IEX',
                'mic'         => 'IEXG',
                'description' => 'A newer exchange focused on fairness and reducing predatory high-frequency trading.'
            ],
            [
                'code'        => 'memx',
                'name'        => 'MEMX',
                'mic'         => 'MEMX',
                'description' => 'A member-owned exchange offering low-cost, transparent equities trading.'
            ],
            [
                'code'        => 'ltse',
                'name'        => 'LTSE',
                'mic'         => 'LTSE',
                'description' => 'Focused on long-term value creation and sustainable governance practices.'
            ],

            // --- OTC & Crypto ---
            [
                'code'        => 'otc',
                'name'        => 'OTC',
                'mic'         => 'OTCM',
                'description' => 'Over-the-counter trading platform for unlisted stocks and smaller companies.'
            ],
            [
                'code'        => 'crypto',
                'name'        => 'Crypto',
                'mic'         => 'XCRY', // Placeholder (not a formal MIC)
                'description' => 'Aggregated crypto exchanges where digital assets like Bitcoin and Ethereum are traded.'
            ],
        ];

        foreach ($exchanges as $exchange) {
            Exchange::insert([
                'name'        => $exchange['name'],
                'code'        => $exchange['code'],
                'mic'         => $exchange['mic'],
                'description' => $exchange['description']
            ]);
        }
    }
}
