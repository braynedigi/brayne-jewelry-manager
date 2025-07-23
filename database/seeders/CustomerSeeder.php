<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        // Add a few customers
        Customer::create([
            'distributor_id' => 1, // make sure you have a distributor with ID 1
            'name' => 'Alice Doe',
            'email' => 'alice@example.com',
            'phone' => '123-456-7890',
            'address' => '123 Main St'
        ]);
        Customer::create([
            'distributor_id' => 1,
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'phone' => '222-333-4444',
            'address' => '456 Park Ave'
        ]);
    }
}

