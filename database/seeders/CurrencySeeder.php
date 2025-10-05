<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                "code"                => 'USD',
                "symbol"              => '$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'PYG',
                "symbol"              => '₲',
                "thousands_separator" => '.',
                "decimal_separator"   => ',',
                'decimal_points'      => 0,
            ],
            [
                "code"                => 'EUR',
                "symbol"              => '€',
                "thousands_separator" => '.',
                "decimal_separator"   => ',',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'GBP',
                "symbol"              => '£',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'JPY',
                "symbol"              => '¥',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 0,
            ],
            [
                "code"                => 'AUD',
                "symbol"              => 'A$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'CAD',
                "symbol"              => 'C$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'CHF',
                "symbol"              => 'CHF',
                "thousands_separator" => "'",
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'CNY',
                "symbol"              => '¥',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'INR',
                "symbol"              => '₹',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'BRL',
                "symbol"              => 'R$',
                "thousands_separator" => '.',
                "decimal_separator"   => ',',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'ZAR',
                "symbol"              => 'R',
                "thousands_separator" => ' ',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'MXN',
                "symbol"              => '$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'SEK',
                "symbol"              => 'kr',
                "thousands_separator" => ' ',
                "decimal_separator"   => ',',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'NZD',
                "symbol"              => 'NZ$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'SGD',
                "symbol"              => 'S$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'HKD',
                "symbol"              => 'HK$',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'KRW',
                "symbol"              => '₩',
                "thousands_separator" => ',',
                "decimal_separator"   => '.',
                'decimal_points'      => 0,
            ],
            [
                "code"                => 'NOK',
                "symbol"              => 'kr',
                "thousands_separator" => ' ',
                "decimal_separator"   => ',',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'TRY',
                "symbol"              => '₺',
                "thousands_separator" => '.',
                "decimal_separator"   => ',',
                'decimal_points'      => 2,
            ],
            [
                "code"                => 'RUB',
                "symbol"              => '₽',
                "thousands_separator" => ' ',
                "decimal_separator"   => ',',
                'decimal_points'      => 2,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::insert([
                'code'                => $currency['code'],
                'symbol'              => $currency['symbol'],
                'thousands_separator' => $currency['thousands_separator'],
                'decimal_separator'   => $currency['decimal_separator'],
                'decimal_points'      => $currency['decimal_points'],
            ]);
        }
    }
}
