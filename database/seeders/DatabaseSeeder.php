<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
//        $this->call(CurrencySeeder::class);
//        $this->call(FxRateSeeder::class);
//        $this->call(ExchangeSeeder::class);

        $this->call(UserSeeder::class);
        $this->call(PreferenceSeeder::class);

//        $this->call(SecuritySeeder::class);
//        $this->call(MarketQuoteSeeder::class);
//        $this->call(MarketDateSeeder::class);

//        $this->call(PortfolioSeeder::class);
//        $this->call(TransactionSeeder::class);

//        $this->call(DividendSeeder::class);
//        $this->call(DividendReceivedSeeder::class);

//        $this->call(CashMovementSeeder::class);
    }
}
