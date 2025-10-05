<?php

namespace Database\Seeders;

use App\Models\MarketDate;
use App\Models\MarketQuote;
use Illuminate\Database\Seeder;

class MarketDateSeeder extends Seeder
{
    public function run(): void
    {
        $dates = MarketQuote::query()
            ->distinct()
            ->orderBy('date')
            ->pluck('date');

        foreach ($dates as $date) {
            MarketDate::insert(['date' => $date]);
        }
    }
}
