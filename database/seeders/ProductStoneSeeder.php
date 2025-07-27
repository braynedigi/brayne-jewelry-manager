<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductStone;

class ProductStoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stones = [
            ['name' => 'Diamond', 'description' => 'Natural diamond', 'sort_order' => 1],
            ['name' => 'Ruby', 'description' => 'Natural ruby', 'sort_order' => 2],
            ['name' => 'Sapphire', 'description' => 'Natural sapphire', 'sort_order' => 3],
            ['name' => 'Emerald', 'description' => 'Natural emerald', 'sort_order' => 4],
            ['name' => 'Pearl', 'description' => 'Natural pearl', 'sort_order' => 5],
            ['name' => 'Opal', 'description' => 'Natural opal', 'sort_order' => 6],
            ['name' => 'Amethyst', 'description' => 'Natural amethyst', 'sort_order' => 7],
            ['name' => 'Citrine', 'description' => 'Natural citrine', 'sort_order' => 8],
            ['name' => 'Garnet', 'description' => 'Natural garnet', 'sort_order' => 9],
            ['name' => 'Topaz', 'description' => 'Natural topaz', 'sort_order' => 10],
            ['name' => 'Aquamarine', 'description' => 'Natural aquamarine', 'sort_order' => 11],
            ['name' => 'Peridot', 'description' => 'Natural peridot', 'sort_order' => 12],
            ['name' => 'Tanzanite', 'description' => 'Natural tanzanite', 'sort_order' => 13],
            ['name' => 'Tourmaline', 'description' => 'Natural tourmaline', 'sort_order' => 14],
            ['name' => 'Zircon', 'description' => 'Natural zircon', 'sort_order' => 15],
            ['name' => 'Lab Created Diamond', 'description' => 'Lab created diamond', 'sort_order' => 16],
            ['name' => 'Lab Created Ruby', 'description' => 'Lab created ruby', 'sort_order' => 17],
            ['name' => 'Lab Created Sapphire', 'description' => 'Lab created sapphire', 'sort_order' => 18],
            ['name' => 'Lab Created Emerald', 'description' => 'Lab created emerald', 'sort_order' => 19],
            ['name' => 'Cubic Zirconia', 'description' => 'Cubic zirconia', 'sort_order' => 20],
            ['name' => 'Moissanite', 'description' => 'Moissanite', 'sort_order' => 21],
        ];

        foreach ($stones as $stone) {
            ProductStone::updateOrCreate(
                ['name' => $stone['name']],
                $stone
            );
        }
    }
} 