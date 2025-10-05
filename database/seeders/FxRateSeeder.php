<?php

namespace Database\Seeders;

use App\Models\FxRate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class FxRateSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch data from API
        $response = Http::get('https://dolar.melizeche.com/api/1.0/');

        if ($response->successful()) {
            $data = $response->json();

            // Use the 'updated' field from the API response
            $dateString = $data['updated'] ?? null;
            $date = $dateString ? date('Y-m-d', strtotime($dateString)) : null;

            if (!$date) {
                $this->command->error('Could not parse date from API response.');

                // Faux Data
                FxRate::insert([
                    'currency_id' => 2, // PYG
                    'buy'         => 7870,
                    'sell'        => 7750,
                    'date'        => Carbon::now()->format('Y-m-d'),
                ]);

                return;
            }

            // Priority order
            $sources = ['maxicambios', 'cambioschaco', 'bcp'];

            $selected = null;

            foreach ($sources as $source) {
                if (
                    isset($data['dolarpy'][$source]) &&
                    !empty($data['dolarpy'][$source]['compra']) &&
                    !empty($data['dolarpy'][$source]['venta'])
                ) {
                    $selected = $data['dolarpy'][$source];
                    break;
                }
            }

            if ($selected) {
                FxRate::insert([
                    'currency_id' => 1, // PYG
                    'buy'         => $selected['compra'],
                    'sell'        => $selected['venta'],
                    'date'        => $date,
                ]);
            } else {
                $this->command->warn('No valid exchange rate source found.');
            }
        } else {
            $this->command->error('Failed to fetch exchange rate data from API.');
        }
    }
}
