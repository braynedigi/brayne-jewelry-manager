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

        // Create products with proper pricing structure
        Product::create([
            'name' => 'Diamond Ring',
            'sku' => 'DR-001',
            'category' => 'Rings',
            'local_pricing' => [
                'Stainless' => 2500.00,
                'Brass Gold' => 2800.00,
                '925 Pure Sterling Silver' => 3200.00,
                '10K Real Gold' => 4500.00,
                '14K Real Gold' => 5800.00,
                '18K Real Gold' => 7200.00,
            ],
            'international_pricing' => [
                'Stainless' => 50.00,
                'Brass Gold' => 56.00,
                '925 Pure Sterling Silver' => 64.00,
                '10K Real Gold' => 90.00,
                '14K Real Gold' => 116.00,
                '18K Real Gold' => 144.00,
            ],
            'metals' => ['Stainless', 'Brass Gold', '925 Pure Sterling Silver', '10K Real Gold', '14K Real Gold', '18K Real Gold'],
            'fonts' => ['Arial', 'Times New Roman', 'Helvetica'],
            'font_requirement' => 1,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Pearl Necklace',
            'sku' => 'PN-001',
            'category' => 'Necklaces',
            'local_pricing' => [
                'Stainless' => 800.00,
                'Brass Gold' => 900.00,
                '925 Pure Sterling Silver' => 1100.00,
                '10K Real Gold' => 1500.00,
                '14K Real Gold' => 1900.00,
                '18K Real Gold' => 2400.00,
            ],
            'international_pricing' => [
                'Stainless' => 16.00,
                'Brass Gold' => 18.00,
                '925 Pure Sterling Silver' => 22.00,
                '10K Real Gold' => 30.00,
                '14K Real Gold' => 38.00,
                '18K Real Gold' => 48.00,
            ],
            'metals' => ['Stainless', 'Brass Gold', '925 Pure Sterling Silver', '10K Real Gold', '14K Real Gold', '18K Real Gold'],
            'fonts' => [],
            'font_requirement' => 0,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Sapphire Earrings',
            'sku' => 'SE-001',
            'category' => 'Earrings',
            'local_pricing' => [
                'Stainless' => 1200.00,
                'Brass Gold' => 1350.00,
                '925 Pure Sterling Silver' => 1600.00,
                '10K Real Gold' => 2200.00,
                '14K Real Gold' => 2800.00,
                '18K Real Gold' => 3500.00,
            ],
            'international_pricing' => [
                'Stainless' => 24.00,
                'Brass Gold' => 27.00,
                '925 Pure Sterling Silver' => 32.00,
                '10K Real Gold' => 44.00,
                '14K Real Gold' => 56.00,
                '18K Real Gold' => 70.00,
            ],
            'metals' => ['Stainless', 'Brass Gold', '925 Pure Sterling Silver', '10K Real Gold', '14K Real Gold', '18K Real Gold'],
            'fonts' => [],
            'font_requirement' => 0,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Gold Bracelet',
            'sku' => 'GB-001',
            'category' => 'Bracelets',
            'local_pricing' => [
                'Stainless' => 600.00,
                'Brass Gold' => 680.00,
                '925 Pure Sterling Silver' => 800.00,
                '10K Real Gold' => 1100.00,
                '14K Real Gold' => 1400.00,
                '18K Real Gold' => 1800.00,
            ],
            'international_pricing' => [
                'Stainless' => 12.00,
                'Brass Gold' => 13.60,
                '925 Pure Sterling Silver' => 16.00,
                '10K Real Gold' => 22.00,
                '14K Real Gold' => 28.00,
                '18K Real Gold' => 36.00,
            ],
            'metals' => ['Stainless', 'Brass Gold', '925 Pure Sterling Silver', '10K Real Gold', '14K Real Gold', '18K Real Gold'],
            'fonts' => ['Arial', 'Times New Roman'],
            'font_requirement' => 1,
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

        // Call other seeders
        $this->call([
            ProductStoneSeeder::class,
            RingSizeSeeder::class,
        ]);
    }
}
