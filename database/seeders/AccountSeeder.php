<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                "user_id"     => 1,
                'currency_id' => 2,
                'name'        => 'Atlas Savings PYG',
                'amount'      => 140860362,
                'order'       => 1,
            ],
            [
                "user_id"     => 1,
                'currency_id' => 1,
                'name'        => 'Atlas Savings USD',
                'amount'      => 1000,
                'order'       => 2,
            ],
            [
                "user_id"     => 1,
                'currency_id' => 2,
                'name'        => 'Basa Savings',
                'amount'      => 23699988,
                'order'       => 3,
            ],
            [
                "user_id"     => 1,
                'currency_id' => 1,
                'name'        => 'Emergency Funds',
                'amount'      => 4000,
                'order'       => 4,
            ],
        ];

        foreach ($accounts as $account) {
            Account::insert([
                'user_id'     => $account['user_id'],
                'currency_id' => $account['currency_id'],
                'name'        => $account['name'],
                'amount'      => $account['amount'],
                'order'       => $account['order'],
            ]);
        }
    }
}
