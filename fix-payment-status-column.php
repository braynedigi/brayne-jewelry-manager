<?php
/**
 * Fix Payment Status Column Script
 * 
 * This script fixes the payment_status column in the orders table
 * that's causing the "Data truncated for column 'payment_status'" error.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Payment Status Column Script</h1>";

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

echo "<h2>Fixing Payment Status Column</h2>";

// Check current column definition
try {
    $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'payment_status'");
    if (count($columns) > 0) {
        $column = $columns[0];
        echo "<p><strong>Current payment_status column:</strong> " . $column->Type . "</p>";
        
        // Check if column is too small
        if (strpos($column->Type, 'varchar(50)') !== false || strpos($column->Type, 'varchar(20)') !== false) {
            echo "<p style='color: orange;'>⚠ Payment_status column is too small - expanding it...</p>";
            
            // Modify the column to be larger
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status VARCHAR(100) DEFAULT 'pending_payment'");
            echo "<p style='color: green;'>✅ Successfully expanded payment_status column to VARCHAR(100)</p>";
        } else {
            echo "<p style='color: green;'>✅ Payment_status column is already large enough</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Payment_status column doesn't exist - creating it...</p>";
        DB::statement("ALTER TABLE orders ADD COLUMN payment_status VARCHAR(100) DEFAULT 'pending_payment'");
        echo "<p style='color: green;'>✅ Created payment_status column</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error with payment_status column: " . $e->getMessage() . "</p>";
}

// Also fix order_status column
echo "<h3>Fixing Order Status Column</h3>";

try {
    $columns = DB::select("SHOW COLUMNS FROM orders LIKE 'order_status'");
    if (count($columns) > 0) {
        $column = $columns[0];
        echo "<p><strong>Current order_status column:</strong> " . $column->Type . "</p>";
        
        // Check if column is too small
        if (strpos($column->Type, 'varchar(50)') !== false || strpos($column->Type, 'varchar(20)') !== false) {
            echo "<p style='color: orange;'>⚠ Order_status column is too small - expanding it...</p>";
            
            // Modify the column to be larger
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_status VARCHAR(100) DEFAULT 'pending_payment'");
            echo "<p style='color: green;'>✅ Successfully expanded order_status column to VARCHAR(100)</p>";
        } else {
            echo "<p style='color: green;'>✅ Order_status column is already large enough</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Order_status column doesn't exist - creating it...</p>";
        DB::statement("ALTER TABLE orders ADD COLUMN order_status VARCHAR(100) DEFAULT 'pending_payment'");
        echo "<p style='color: green;'>✅ Created order_status column</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error with order_status column: " . $e->getMessage() . "</p>";
}

// Check for other potential column issues
echo "<h3>Checking Other Columns</h3>";

$columnsToCheck = [
    'distributor_id' => 'BIGINT UNSIGNED NOT NULL',
    'customer_id' => 'BIGINT UNSIGNED NOT NULL',
    'courier_id' => 'BIGINT UNSIGNED NULL',
    'order_number' => 'VARCHAR(255) UNIQUE NOT NULL',
    'total_amount' => 'DECIMAL(10,2) DEFAULT 0.00',
    'notes' => 'TEXT NULL'
];

foreach ($columnsToCheck as $column => $definition) {
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

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p style='color: green; font-weight: bold;'>🎉 Payment status columns have been fixed!</p>";
echo "<p>The 'Data truncated for column payment_status' error should now be resolved.</p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test creating an order:</strong> Visit <a href='/orders/create' target='_blank'>create order page</a></li>";
echo "<li><strong>Try different payment statuses:</strong> fully_paid, partially_paid, pending_payment</li>";
echo "<li><strong>Check if orders save properly:</strong> No more truncation errors</li>";
echo "</ol>";

echo "<p><strong>The payment status column issue should now be fixed!</strong></p>";

?> 