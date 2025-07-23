<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Courier;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@jewelry.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create distributor users
        $distributor1 = User::create([
            'name' => 'John Distributor',
            'email' => 'distributor1@jewelry.com',
            'password' => Hash::make('password'),
            'role' => 'distributor',
        ]);

        $distributor2 = User::create([
            'name' => 'Jane Distributor',
            'email' => 'distributor2@jewelry.com',
            'password' => Hash::make('password'),
            'role' => 'distributor',
        ]);

        // Create factory user
        $factory = User::create([
            'name' => 'Factory Manager',
            'email' => 'factory@jewelry.com',
            'password' => Hash::make('password'),
            'role' => 'factory',
        ]);

        // Create distributor profiles
        Distributor::create([
            'user_id' => $distributor1->id,
            'company_name' => 'Golden Jewelers',
            'phone' => '+1-555-0101',
            'street' => '123 Main Street',
            'city' => 'New York',
            'province' => 'NY',
            'country' => 'USA',
            'is_international' => false,
        ]);

        Distributor::create([
            'user_id' => $distributor2->id,
            'company_name' => 'Silver & Gold Co.',
            'phone' => '+1-555-0202',
            'street' => '456 Oak Avenue',
            'city' => 'Los Angeles',
            'province' => 'CA',
            'country' => 'USA',
            'is_international' => false,
        ]);

        // Create products
        Product::create([
            'name' => 'Diamond Ring',
            'description' => 'Beautiful 18k gold diamond ring',
            'price' => 2500.00,
            'sku' => 'DR-001',
            'category' => 'Rings',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Pearl Necklace',
            'description' => 'Elegant freshwater pearl necklace',
            'price' => 800.00,
            'sku' => 'PN-001',
            'category' => 'Necklaces',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Sapphire Earrings',
            'description' => 'Stunning sapphire and diamond earrings',
            'price' => 1200.00,
            'sku' => 'SE-001',
            'category' => 'Earrings',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Gold Bracelet',
            'description' => 'Classic 14k gold bracelet',
            'price' => 600.00,
            'sku' => 'GB-001',
            'category' => 'Bracelets',
            'is_active' => true,
        ]);

        // Create couriers
        Courier::create([
            'name' => 'Express Delivery',
            'phone' => '+1-555-0303',
            'email' => 'express@delivery.com',
            'is_active' => true,
        ]);

        Courier::create([
            'name' => 'Fast Shipping Co.',
            'phone' => '+1-555-0404',
            'email' => 'fast@shipping.com',
            'is_active' => true,
        ]);

        Courier::create([
            'name' => 'Premium Logistics',
            'phone' => '+1-555-0505',
            'email' => 'premium@logistics.com',
            'is_active' => true,
        ]);

        // Call the ProductCategorySeeder to populate categories, metals, stones, fonts, and ring sizes
        $this->call([
            ProductCategorySeeder::class,
        ]);
    }
}
