<?php
/**
 * Fix Orders Courier Column Script
 * 
 * This script adds the missing courier_id column to the orders table
 * that's causing the "Unknown column 'courier_id'" error.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Orders Courier Column Script</h1>";

// Check if we're in the right directory
if (!file_exists('public/index.php')) {
    echo "<p style='color: red;'>❌ This script must be run from the Laravel root directory!</p>";
    echo "<p>Make sure you're in the directory that contains the 'public' folder.</p>";
    exit;
}

// Start Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Fixing Orders Table - Adding courier_id Column</h2>";

// Check if courier_id column exists
try {
    $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'courier_id'");
    if (count($columns) > 0) {
        echo "<p style='color: green;'>✅ 'courier_id' column already exists in orders table</p>";
    } else {
        echo "<p style='color: orange;'>⚠ 'courier_id' column missing - adding it now...</p>";
        
        // Add courier_id column
        DB::statement("ALTER TABLE orders ADD COLUMN courier_id BIGINT UNSIGNED NULL AFTER customer_id");
        
        // Add foreign key constraint if couriers table exists
        try {
            $courierTableExists = DB::select("SHOW TABLES LIKE 'couriers'");
            if (count($courierTableExists) > 0) {
                DB::statement("ALTER TABLE orders ADD CONSTRAINT fk_orders_courier FOREIGN KEY (courier_id) REFERENCES couriers(id)");
                echo "<p style='color: green;'>✅ Added foreign key constraint for courier_id</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Couriers table doesn't exist - skipping foreign key constraint</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠ Could not add foreign key constraint: " . $e->getMessage() . "</p>";
        }
        
        echo "<p style='color: green;'>✅ Successfully added 'courier_id' column to orders table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking/adding courier_id column: " . $e->getMessage() . "</p>";
}

// Check for other missing columns that might be needed
$requiredColumns = [
    'distributor_id' => 'BIGINT UNSIGNED NOT NULL',
    'customer_id' => 'BIGINT UNSIGNED NOT NULL',
    'order_number' => 'VARCHAR(255) UNIQUE NOT NULL',
    'total_amount' => 'DECIMAL(10,2) DEFAULT 0.00',
    'payment_status' => 'VARCHAR(50) DEFAULT "pending_payment"',
    'order_status' => 'VARCHAR(50) DEFAULT "pending_payment"',
    'notes' => 'TEXT NULL',
    'created_at' => 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP',
    'updated_at' => 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
];

echo "<h3>Checking Other Required Columns</h3>";

foreach ($requiredColumns as $column => $definition) {
    try {
        $columns = DB::select("SHOW COLUMNS FROM orders LIKE '$column'");
        if (count($columns) > 0) {
            echo "<p style='color: green;'>✅ '$column' column exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ '$column' column missing - adding it...</p>";
            DB::statement("ALTER TABLE orders ADD COLUMN $column $definition");
            echo "<p style='color: green;'>✅ Added '$column' column</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error with '$column' column: " . $e->getMessage() . "</p>";
    }
}

// Check if orders table exists
try {
    $tableExists = DB::select("SHOW TABLES LIKE 'orders'");
    if (count($tableExists) == 0) {
        echo "<p style='color: red;'>❌ Orders table doesn't exist - creating it...</p>";
        
        // Create orders table with all required columns
        DB::statement("
            CREATE TABLE orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                distributor_id BIGINT UNSIGNED NOT NULL,
                customer_id BIGINT UNSIGNED NOT NULL,
                courier_id BIGINT UNSIGNED NULL,
                order_number VARCHAR(255) UNIQUE NOT NULL,
                total_amount DECIMAL(10,2) DEFAULT 0.00,
                payment_status VARCHAR(50) DEFAULT 'pending_payment',
                order_status VARCHAR(50) DEFAULT 'pending_payment',
                notes TEXT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_orders_distributor (distributor_id),
                INDEX idx_orders_customer (customer_id),
                INDEX idx_orders_courier (courier_id),
                INDEX idx_orders_status (order_status)
            )
        ");
        
        echo "<p style='color: green;'>✅ Created orders table with all required columns</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error creating orders table: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p style='color: green; font-weight: bold;'>🎉 Orders table has been fixed!</p>";
echo "<p>The 'Unknown column courier_id' error should now be resolved.</p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test creating an order:</strong> Visit <a href='/orders/create' target='_blank'>create order page</a></li>";
echo "<li><strong>Check if the error is gone:</strong> Try submitting the order form</li>";
echo "<li><strong>Verify order creation:</strong> Check if orders are saved properly</li>";
echo "</ol>";

echo "<p><strong>The courier_id column issue should now be fixed!</strong></p>";

?> 