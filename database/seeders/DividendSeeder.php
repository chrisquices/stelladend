<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\Security;
use App\Models\TransactionMetric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DividendSeeder extends Seeder
{
    public function run(): void
    {
        $apiKey = config('polygon.api_key');

        $securityIds = TransactionMetric::select('security_id')->get()->pluck('security_id')->toArray();
        $securities = Security::find($securityIds);

        foreach ($securities as $security) {

            $url = "https://api.polygon.io/v3/reference/dividends?ticker=$security->ticker&order=asc&limit=1000&sort=ex_dividend_date&apiKey={$apiKey}";
            $response = Http::get($url);
            $dividends = $response->json()['results'];

            foreach ($dividends as $dividend) {
                Dividend::insert([
                    'security_id'      => $security->id,
                    'ex_dividend_date' => $dividend['ex_dividend_date'] ?? null,
                    'declaration_date' => $dividend['declaration_date'] ?? null,
                    'record_date'      => $dividend['record_date'] ?? null,
                    'payment_date'     => $dividend['pay_date'] ?? null,
                    'amount'           => $dividend['cash_amount'] ?? null,
                    'currency'         => $dividend['currency'] ?? null,
                ]);
            }

            // Set dividend payout frequency
            if (collect($dividends)->count() > 0) {
                $dividendPayoutFrequency = collect($dividends)->last()['frequency'];

                $security->dividend_payout_frequency = $dividendPayoutFrequency;
                $security->save();
            }

            sleep(15);
        }
    }
}
