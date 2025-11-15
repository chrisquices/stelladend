<?php

namespace Database\Seeders;

use App\Models\Exchange;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email'    => 'chrisquices@gmail.com',
                'password' => '$2y$12$RRNRvgEu6otXgxIDUt8IGeM6yCEaNub/0UZPGBDxmoUn8HO3sC04C',
            ],
        ];

        foreach ($users as $user) {
            User::insert([
                'email'    => $user['email'],
                'password' => $user['password'],
            ]);
        }
    }
}
