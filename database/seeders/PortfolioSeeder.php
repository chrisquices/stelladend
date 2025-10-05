<?php

namespace Database\Seeders;

use App\Models\Exchange;
use App\Models\Portfolio;
use App\Models\PortfolioMetric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $portfolios = [
            [
                'id'         => 1,
                'uid'        => Str::uuid()->toString(),
                'user_id'    => 1,
                'name'       => 'FIRE Portfolio',
                'is_default' => true,
            ],
        ];

        foreach ($portfolios as $portfolio) {
            Portfolio::insert([
                'uid'        => $portfolio['uid'],
                'user_id'    => $portfolio['user_id'],
                'name'       => $portfolio['name'],
                'is_default' => $portfolio['is_default']
            ]);
        }
    }
}
