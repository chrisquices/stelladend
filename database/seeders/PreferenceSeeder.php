<?php

namespace Database\Seeders;

use App\Models\Exchange;
use App\Models\Portfolio;
use App\Models\PortfolioMetric;
use App\Models\Preference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $preferences = [
            [
                'user_id'               => 1,
                'secondary_currency_id' => null,
                'theme'                 => 'emerald-light',
            ],
        ];

        foreach ($preferences as $preference) {
            Preference::insert([
                'user_id'               => $preference['user_id'],
                'theme'                 => $preference['theme'],
                'secondary_currency_id' => $preference['secondary_currency_id'],
                'has_been_welcomed'     => false,
            ]);
        }
    }
}
