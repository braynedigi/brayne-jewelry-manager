<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RingSize;

class RingSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ringSizes = [
            ['size' => '3', 'description' => 'US Ring Size 3', 'sort_order' => 1],
            ['size' => '3.5', 'description' => 'US Ring Size 3.5', 'sort_order' => 2],
            ['size' => '4', 'description' => 'US Ring Size 4', 'sort_order' => 3],
            ['size' => '4.5', 'description' => 'US Ring Size 4.5', 'sort_order' => 4],
            ['size' => '5', 'description' => 'US Ring Size 5', 'sort_order' => 5],
            ['size' => '5.5', 'description' => 'US Ring Size 5.5', 'sort_order' => 6],
            ['size' => '6', 'description' => 'US Ring Size 6', 'sort_order' => 7],
            ['size' => '6.5', 'description' => 'US Ring Size 6.5', 'sort_order' => 8],
            ['size' => '7', 'description' => 'US Ring Size 7', 'sort_order' => 9],
            ['size' => '7.5', 'description' => 'US Ring Size 7.5', 'sort_order' => 10],
            ['size' => '8', 'description' => 'US Ring Size 8', 'sort_order' => 11],
            ['size' => '8.5', 'description' => 'US Ring Size 8.5', 'sort_order' => 12],
            ['size' => '9', 'description' => 'US Ring Size 9', 'sort_order' => 13],
            ['size' => '9.5', 'description' => 'US Ring Size 9.5', 'sort_order' => 14],
            ['size' => '10', 'description' => 'US Ring Size 10', 'sort_order' => 15],
            ['size' => '10.5', 'description' => 'US Ring Size 10.5', 'sort_order' => 16],
            ['size' => '11', 'description' => 'US Ring Size 11', 'sort_order' => 17],
            ['size' => '11.5', 'description' => 'US Ring Size 11.5', 'sort_order' => 18],
            ['size' => '12', 'description' => 'US Ring Size 12', 'sort_order' => 19],
            ['size' => '12.5', 'description' => 'US Ring Size 12.5', 'sort_order' => 20],
            ['size' => '13', 'description' => 'US Ring Size 13', 'sort_order' => 21],
        ];

        foreach ($ringSizes as $ringSize) {
            RingSize::updateOrCreate(
                ['size' => $ringSize['size']],
                $ringSize
            );
        }
    }
} 