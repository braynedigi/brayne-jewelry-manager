<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\ProductMetal;
use App\Models\ProductStone;
use App\Models\ProductFont;
use App\Models\RingSize;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product Categories
        $categories = [
            ['name' => 'Rings', 'description' => 'Engagement rings, wedding bands, fashion rings', 'sort_order' => 1],
            ['name' => 'Necklaces', 'description' => 'Pendants, chains, chokers', 'sort_order' => 2],
            ['name' => 'Earrings', 'description' => 'Studs, hoops, drop earrings', 'sort_order' => 3],
            ['name' => 'Bracelets', 'description' => 'Bangles, chains, cuffs', 'sort_order' => 4],
            ['name' => 'Pendants', 'description' => 'Standalone pendant pieces', 'sort_order' => 5],
            ['name' => 'Chains', 'description' => 'Necklace chains, bracelet chains', 'sort_order' => 6],
            ['name' => 'Anklets', 'description' => 'Ankle bracelets and chains', 'sort_order' => 7],
            ['name' => 'Brooches', 'description' => 'Decorative pins and brooches', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }

        // Product Metals
        $metals = [
            ['name' => '14K Yellow Gold', 'description' => '14 karat yellow gold', 'sort_order' => 1],
            ['name' => '14K White Gold', 'description' => '14 karat white gold', 'sort_order' => 2],
            ['name' => '14K Rose Gold', 'description' => '14 karat rose gold', 'sort_order' => 3],
            ['name' => '18K Yellow Gold', 'description' => '18 karat yellow gold', 'sort_order' => 4],
            ['name' => '18K White Gold', 'description' => '18 karat white gold', 'sort_order' => 5],
            ['name' => '18K Rose Gold', 'description' => '18 karat rose gold', 'sort_order' => 6],
            ['name' => 'Platinum', 'description' => 'Platinum metal', 'sort_order' => 7],
            ['name' => 'Sterling Silver', 'description' => '925 sterling silver', 'sort_order' => 8],
            ['name' => 'Palladium', 'description' => 'Palladium metal', 'sort_order' => 9],
        ];

        foreach ($metals as $metal) {
            ProductMetal::create($metal);
        }

        // Product Stones
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
            ['name' => 'Aquamarine', 'description' => 'Natural aquamarine', 'sort_order' => 10],
            ['name' => 'Tanzanite', 'description' => 'Natural tanzanite', 'sort_order' => 11],
            ['name' => 'Topaz', 'description' => 'Natural topaz', 'sort_order' => 12],
            ['name' => 'No Stone', 'description' => 'Plain metal without stones', 'sort_order' => 13],
        ];

        foreach ($stones as $stone) {
            ProductStone::create($stone);
        }

        // Product Fonts
        $fonts = [
            ['name' => 'Classic Serif', 'description' => 'Traditional serif font', 'sort_order' => 1],
            ['name' => 'Modern Sans', 'description' => 'Clean modern sans-serif', 'sort_order' => 2],
            ['name' => 'Script', 'description' => 'Elegant script font', 'sort_order' => 3],
            ['name' => 'Bold', 'description' => 'Bold weight font', 'sort_order' => 4],
            ['name' => 'Italic', 'description' => 'Italic style font', 'sort_order' => 5],
            ['name' => 'Calligraphy', 'description' => 'Calligraphic style font', 'sort_order' => 6],
            ['name' => 'Gothic', 'description' => 'Gothic style font', 'sort_order' => 7],
            ['name' => 'Minimal', 'description' => 'Minimalist thin font', 'sort_order' => 8],
        ];

        foreach ($fonts as $font) {
            ProductFont::create($font);
        }

        // Ring Sizes
        $ringSizes = [
            ['size' => '3', 'description' => 'Small ring size', 'sort_order' => 1],
            ['size' => '3.5', 'description' => 'Small ring size', 'sort_order' => 2],
            ['size' => '4', 'description' => 'Small ring size', 'sort_order' => 3],
            ['size' => '4.5', 'description' => 'Small ring size', 'sort_order' => 4],
            ['size' => '5', 'description' => 'Small ring size', 'sort_order' => 5],
            ['size' => '5.5', 'description' => 'Small-medium ring size', 'sort_order' => 6],
            ['size' => '6', 'description' => 'Medium ring size', 'sort_order' => 7],
            ['size' => '6.5', 'description' => 'Medium ring size', 'sort_order' => 8],
            ['size' => '7', 'description' => 'Medium ring size', 'sort_order' => 9],
            ['size' => '7.5', 'description' => 'Medium-large ring size', 'sort_order' => 10],
            ['size' => '8', 'description' => 'Large ring size', 'sort_order' => 11],
            ['size' => '8.5', 'description' => 'Large ring size', 'sort_order' => 12],
            ['size' => '9', 'description' => 'Large ring size', 'sort_order' => 13],
            ['size' => '9.5', 'description' => 'Large ring size', 'sort_order' => 14],
            ['size' => '10', 'description' => 'Extra large ring size', 'sort_order' => 15],
            ['size' => '10.5', 'description' => 'Extra large ring size', 'sort_order' => 16],
            ['size' => '11', 'description' => 'Extra large ring size', 'sort_order' => 17],
            ['size' => '11.5', 'description' => 'Extra large ring size', 'sort_order' => 18],
            ['size' => '12', 'description' => 'Extra large ring size', 'sort_order' => 19],
        ];

        foreach ($ringSizes as $ringSize) {
            RingSize::create($ringSize);
        }
    }
}
