<?php

namespace Database\Seeders;

use App\Models\Dividend;
use App\Models\DividendReceived;
use App\Models\Security;
use Illuminate\Database\Seeder;

class DividendReceivedSeeder extends Seeder
{
    public function run(): void
    {
        $jepi = Security::where('ticker', 'JEPI')->first();

        $dividendsReceived = [
            [
                'portfolio_id'    => 1,
                'security_id'     => $jepi->id,
                'dividend_id'     => Dividend::query()
                    ->where('security_id', $jepi->id)
                    ->where('ex_dividend_date', '2025-05-01')
                    ->where('declaration_date', '2025-01-03')
                    ->where('record_date', '2025-05-01')
                    ->where('payment_date', '2025-05-05')
                    ->first()->id,
                'shares'          => 100,
                'dividend_amount' => 0.48792,
                'total'           => 48.79,
                'date'            => '05-05-2025',
            ],
            [
                'portfolio_id'    => 1,
                'security_id'     => $jepi->id,
                'dividend_id'     => Dividend::query()
                    ->where('security_id', $jepi->id)
                    ->where('ex_dividend_date', '2025-06-02')
                    ->where('declaration_date', '2025-01-03')
                    ->where('record_date', '2025-06-02')
                    ->where('payment_date', '2025-06-04')
                    ->first()->id,
                'shares'          => 206.8767,
                'dividend_amount' => 0.54001,
                'total'           => 111.72,
                'date'            => '06-04-2025',
            ],
        ];

        foreach ($dividendsReceived as $cashMovement) {
            DividendReceived::insert([
                'portfolio_id'    => $cashMovement['portfolio_id'],
                'security_id'     => $cashMovement['security_id'],
                'dividend_id'     => $cashMovement['dividend_id'],
                'shares'          => $cashMovement['shares'],
                'dividend_amount' => $cashMovement['dividend_amount'],
                'total'           => $cashMovement['total'],
                'date'            => $cashMovement['date'],
            ]);
        }
    }
}
