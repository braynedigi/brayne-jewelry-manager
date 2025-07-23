<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing parent categories
        $rings = ProductCategory::where('name', 'Rings')->first();
        $necklaces = ProductCategory::where('name', 'Necklaces')->first();
        $earrings = ProductCategory::where('name', 'Earrings')->first();
        $bracelets = ProductCategory::where('name', 'Bracelets')->first();
        $pendants = ProductCategory::where('name', 'Pendants')->first();

        // Ring subcategories
        if ($rings) {
            $ringSubcategories = [
                ['name' => 'Engagement Rings', 'description' => 'Diamond engagement rings', 'sort_order' => 1],
                ['name' => 'Wedding Bands', 'description' => 'Wedding and anniversary bands', 'sort_order' => 2],
                ['name' => 'Fashion Rings', 'description' => 'Decorative fashion rings', 'sort_order' => 3],
                ['name' => 'Promise Rings', 'description' => 'Promise and commitment rings', 'sort_order' => 4],
                ['name' => 'Cocktail Rings', 'description' => 'Statement cocktail rings', 'sort_order' => 5],
            ];

            foreach ($ringSubcategories as $subcategory) {
                ProductCategory::create([
                    'name' => $subcategory['name'],
                    'description' => $subcategory['description'],
                    'parent_id' => $rings->id,
                    'sort_order' => $subcategory['sort_order'],
                    'is_active' => true,
                ]);
            }
        }

        // Necklace subcategories
        if ($necklaces) {
            $necklaceSubcategories = [
                ['name' => 'Pendant Necklaces', 'description' => 'Necklaces with pendant charms', 'sort_order' => 1],
                ['name' => 'Chain Necklaces', 'description' => 'Simple chain necklaces', 'sort_order' => 2],
                ['name' => 'Choker Necklaces', 'description' => 'Short choker style necklaces', 'sort_order' => 3],
                ['name' => 'Layered Necklaces', 'description' => 'Multi-layered necklace sets', 'sort_order' => 4],
                ['name' => 'Statement Necklaces', 'description' => 'Bold statement necklaces', 'sort_order' => 5],
            ];

            foreach ($necklaceSubcategories as $subcategory) {
                ProductCategory::create([
                    'name' => $subcategory['name'],
                    'description' => $subcategory['description'],
                    'parent_id' => $necklaces->id,
                    'sort_order' => $subcategory['sort_order'],
                    'is_active' => true,
                ]);
            }
        }

        // Earring subcategories
        if ($earrings) {
            $earringSubcategories = [
                ['name' => 'Stud Earrings', 'description' => 'Classic stud earrings', 'sort_order' => 1],
                ['name' => 'Hoop Earrings', 'description' => 'Circular hoop earrings', 'sort_order' => 2],
                ['name' => 'Drop Earrings', 'description' => 'Elegant drop earrings', 'sort_order' => 3],
                ['name' => 'Chandelier Earrings', 'description' => 'Ornate chandelier earrings', 'sort_order' => 4],
                ['name' => 'Ear Cuffs', 'description' => 'Modern ear cuff designs', 'sort_order' => 5],
            ];

            foreach ($earringSubcategories as $subcategory) {
                ProductCategory::create([
                    'name' => $subcategory['name'],
                    'description' => $subcategory['description'],
                    'parent_id' => $earrings->id,
                    'sort_order' => $subcategory['sort_order'],
                    'is_active' => true,
                ]);
            }
        }

        // Bracelet subcategories
        if ($bracelets) {
            $braceletSubcategories = [
                ['name' => 'Bangle Bracelets', 'description' => 'Rigid bangle bracelets', 'sort_order' => 1],
                ['name' => 'Chain Bracelets', 'description' => 'Flexible chain bracelets', 'sort_order' => 2],
                ['name' => 'Cuff Bracelets', 'description' => 'Open cuff bracelets', 'sort_order' => 3],
                ['name' => 'Charm Bracelets', 'description' => 'Bracelets with charms', 'sort_order' => 4],
                ['name' => 'Tennis Bracelets', 'description' => 'Diamond tennis bracelets', 'sort_order' => 5],
            ];

            foreach ($braceletSubcategories as $subcategory) {
                ProductCategory::create([
                    'name' => $subcategory['name'],
                    'description' => $subcategory['description'],
                    'parent_id' => $bracelets->id,
                    'sort_order' => $subcategory['sort_order'],
                    'is_active' => true,
                ]);
            }
        }

        // Pendant subcategories
        if ($pendants) {
            $pendantSubcategories = [
                ['name' => 'Diamond Pendants', 'description' => 'Diamond pendant charms', 'sort_order' => 1],
                ['name' => 'Gemstone Pendants', 'description' => 'Colored gemstone pendants', 'sort_order' => 2],
                ['name' => 'Initial Pendants', 'description' => 'Personalized initial pendants', 'sort_order' => 3],
                ['name' => 'Religious Pendants', 'description' => 'Religious and spiritual pendants', 'sort_order' => 4],
                ['name' => 'Symbol Pendants', 'description' => 'Symbolic and meaningful pendants', 'sort_order' => 5],
            ];

            foreach ($pendantSubcategories as $subcategory) {
                ProductCategory::create([
                    'name' => $subcategory['name'],
                    'description' => $subcategory['description'],
                    'parent_id' => $pendants->id,
                    'sort_order' => $subcategory['sort_order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
