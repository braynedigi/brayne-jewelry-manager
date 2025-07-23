<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Distributor;
use App\Models\User;

class DistributorSeeder extends Seeder
{
    public function run()
    {
        // Create a user for the distributor
        $user = User::create([
            'name' => 'Sample Distributor',
            'email' => 'distributor@example.com',
            'password' => bcrypt('password'),
            'role' => 'distributor',
        ]);

        // Create the distributor
        Distributor::create([
            'user_id' => $user->id,
        ]);
    }
}

