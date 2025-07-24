<?php
/**
 * Fix Null Dates Script
 * 
 * This script fixes null created_at and updated_at values in the database
 * that are causing "Call to a member function format() on null" errors.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Null Dates Script</h1>";

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

echo "<h2>Fixing Null Date Values</h2>";

$currentTime = now();

// Fix products table
echo "<h3>Fixing Products Table</h3>";

try {
    $nullCreatedProducts = DB::table('products')->whereNull('created_at')->count();
    $nullUpdatedProducts = DB::table('products')->whereNull('updated_at')->count();
    
    echo "<p><strong>Products with null created_at:</strong> $nullCreatedProducts</p>";
    echo "<p><strong>Products with null updated_at:</strong> $nullUpdatedProducts</p>";
    
    if ($nullCreatedProducts > 0) {
        DB::table('products')->whereNull('created_at')->update(['created_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null created_at values in products table</p>";
    }
    
    if ($nullUpdatedProducts > 0) {
        DB::table('products')->whereNull('updated_at')->update(['updated_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null updated_at values in products table</p>";
    }
    
    if ($nullCreatedProducts == 0 && $nullUpdatedProducts == 0) {
        echo "<p style='color: green;'>✅ No null date values found in products table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fixing products table: " . $e->getMessage() . "</p>";
}

// Fix orders table
echo "<h3>Fixing Orders Table</h3>";

try {
    $nullCreatedOrders = DB::table('orders')->whereNull('created_at')->count();
    $nullUpdatedOrders = DB::table('orders')->whereNull('updated_at')->count();
    
    echo "<p><strong>Orders with null created_at:</strong> $nullCreatedOrders</p>";
    echo "<p><strong>Orders with null updated_at:</strong> $nullUpdatedOrders</p>";
    
    if ($nullCreatedOrders > 0) {
        DB::table('orders')->whereNull('created_at')->update(['created_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null created_at values in orders table</p>";
    }
    
    if ($nullUpdatedOrders > 0) {
        DB::table('orders')->whereNull('updated_at')->update(['updated_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null updated_at values in orders table</p>";
    }
    
    if ($nullCreatedOrders == 0 && $nullUpdatedOrders == 0) {
        echo "<p style='color: green;'>✅ No null date values found in orders table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fixing orders table: " . $e->getMessage() . "</p>";
}

// Fix customers table
echo "<h3>Fixing Customers Table</h3>";

try {
    $nullCreatedCustomers = DB::table('customers')->whereNull('created_at')->count();
    $nullUpdatedCustomers = DB::table('customers')->whereNull('updated_at')->count();
    
    echo "<p><strong>Customers with null created_at:</strong> $nullCreatedCustomers</p>";
    echo "<p><strong>Customers with null updated_at:</strong> $nullUpdatedCustomers</p>";
    
    if ($nullCreatedCustomers > 0) {
        DB::table('customers')->whereNull('created_at')->update(['created_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null created_at values in customers table</p>";
    }
    
    if ($nullUpdatedCustomers > 0) {
        DB::table('customers')->whereNull('updated_at')->update(['updated_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null updated_at values in customers table</p>";
    }
    
    if ($nullCreatedCustomers == 0 && $nullUpdatedCustomers == 0) {
        echo "<p style='color: green;'>✅ No null date values found in customers table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fixing customers table: " . $e->getMessage() . "</p>";
}

// Fix distributors table
echo "<h3>Fixing Distributors Table</h3>";

try {
    $nullCreatedDistributors = DB::table('distributors')->whereNull('created_at')->count();
    $nullUpdatedDistributors = DB::table('distributors')->whereNull('updated_at')->count();
    
    echo "<p><strong>Distributors with null created_at:</strong> $nullCreatedDistributors</p>";
    echo "<p><strong>Distributors with null updated_at:</strong> $nullUpdatedDistributors</p>";
    
    if ($nullCreatedDistributors > 0) {
        DB::table('distributors')->whereNull('created_at')->update(['created_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null created_at values in distributors table</p>";
    }
    
    if ($nullUpdatedDistributors > 0) {
        DB::table('distributors')->whereNull('updated_at')->update(['updated_at' => $currentTime]);
        echo "<p style='color: green;'>✅ Fixed null updated_at values in distributors table</p>";
    }
    
    if ($nullCreatedDistributors == 0 && $nullUpdatedDistributors == 0) {
        echo "<p style='color: green;'>✅ No null date values found in distributors table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fixing distributors table: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p style='color: green; font-weight: bold;'>🎉 Null date values have been fixed!</p>";
echo "<p>The 'Call to a member function format() on null' error should now be resolved.</p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test the dashboard:</strong> Visit <a href='/admin/dashboard' target='_blank'>admin dashboard</a></li>";
echo "<li><strong>Test distributor access:</strong> Visit <a href='/check-current-user.php?force=1' target='_blank'>check current user</a></li>";
echo "<li><strong>Try creating orders:</strong> The application should now work without errors</li>";
echo "</ol>";

echo "<p><strong>The dashboard should now load without the format() error!</strong></p>";

?> 