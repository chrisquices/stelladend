<?php

namespace Database\Seeders;

use App\Helpers\ColorHelper;
use App\Models\Security;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(database_path('data/securities/index.json'));
        $securities = collect(json_decode($json, true));
//        $securities = $securities->whereIn('ticker', ['SCHD', 'JEPI', 'AVGO']);

        foreach ($securities as $security) {
            Security::create([
                'id'          => $security['id'],
                'uid'         => $security['uid'],
                'exchange_id' => $security['exchange_id'],
                'ticker'      => $security['ticker'],
                'name'        => $security['name'],
                'description' => $security['description'],
                'type'        => $security['type'],
                'market'      => $security['market'],
                'color'       => ColorHelper::generateRandomBrightColor(),
                'is_active'   => $security['is_active'],
            ]);
        }
    }
}
