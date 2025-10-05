<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Rent',
            'Mortgage',
            'Electricity',
            'Water',
            'Gas',
            'Internet',
            'Mobile Phone',
            'Groceries',
            'Fuel',
            'Public Transport',
            'Taxi',
            'Ride Sharing',
            'Car Maintenance',
            'Car Insurance',
            'Parking',
            'Tolls',
            'Health Insurance',
            'Home Insurance',
            'Life Insurance',
            'Doctor',
            'Medication',
            'Dental',
            'Vision',
            'Dining Out',
            'Coffee',
            'Snacks',
            'Streaming Services',
            'Movies',
            'Concerts',
            'Gaming',
            'Clothing',
            'Electronics',
            'Home Goods',
            'Hobbies',
            'Gym Membership',
            'Fitness Equipment',
            'Flights',
            'Hotel',
            'Vacation',
            'Books',
            'Online Courses',
            'Tuition',
            'Certifications',
            'Workshops',
            'Office Supplies',
            'Software Subscriptions',
            'Freelance Tools',
            'Coworking Space',
            'Client Expenses',
            'Gifts Given',
            'Charity',
            'Pet Supplies',
            'Vet Bills',
            'Haircut',
            'Skincare',
            'Babysitting',
            'Childcare',
            'Bank Fees',
            'Taxes',
            'Home Repairs',
            'Furniture',
            'Netflix',
            'Spotify',
            'Amazon Prime',
            'iCloud',
            'Google One',
            'Salary',
            'Freelance Income',
            'Side Hustle',
            'Investment Income',
            'Dividends',
            'Cashback',
            'Gift Received',
            'Sale of Item',
            'Refund'
        ];

        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->type = 'account';
            $newCategory->name = $category;
            $newCategory->save();
        }
    }
}
