<?php
/**
 * Debug Customer Issue Script
 * 
 * This script diagnoses the customer-distributor relationship issue
 * that's causing the "You can only create orders for your own customers" error.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Debug Customer Issue Script</h1>";

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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Customer;
use App\Models\Distributor;

echo "<h2>Database Connection Test</h2>";

try {
    DB::connection()->getPdo();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Table Structure Check</h2>";

// Check if tables exist
$tables = ['users', 'distributors', 'customers'];
foreach ($tables as $table) {
    try {
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if (count($exists) > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' does not exist</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Data Analysis</h2>";

// Check users
try {
    $users = User::all();
    echo "<p><strong>Total users:</strong> " . $users->count() . "</p>";
    
    $distributorUsers = User::where('role', 'distributor')->get();
    echo "<p><strong>Distributor users:</strong> " . $distributorUsers->count() . "</p>";
    
    foreach ($distributorUsers as $user) {
        echo "<p>- User ID: {$user->id}, Name: {$user->name}, Email: {$user->email}</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking users: " . $e->getMessage() . "</p>";
}

// Check distributors
try {
    $distributors = Distributor::all();
    echo "<p><strong>Total distributors:</strong> " . $distributors->count() . "</p>";
    
    foreach ($distributors as $distributor) {
        $user = User::find($distributor->user_id);
        $userName = $user ? $user->name : 'Unknown';
        echo "<p>- Distributor ID: {$distributor->id}, User: {$userName}, Company: {$distributor->company_name}</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking distributors: " . $e->getMessage() . "</p>";
}

// Check customers
try {
    $customers = Customer::all();
    echo "<p><strong>Total customers:</strong> " . $customers->count() . "</p>";
    
    $unassignedCustomers = Customer::whereNull('distributor_id')->orWhere('distributor_id', 0)->get();
    echo "<p><strong>Unassigned customers:</strong> " . $unassignedCustomers->count() . "</p>";
    
    foreach ($customers as $customer) {
        $distributor = Distributor::find($customer->distributor_id);
        $distributorName = $distributor ? $distributor->company_name : 'Unassigned';
        echo "<p>- Customer ID: {$customer->id}, Name: {$customer->name}, Distributor: {$distributorName}</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking customers: " . $e->getMessage() . "</p>";
}

echo "<h2>Column Structure Check</h2>";

// Check distributor_id column in customers table
try {
    $columns = DB::select("SHOW COLUMNS FROM customers LIKE 'distributor_id'");
    if (count($columns) > 0) {
        echo "<p style='color: green;'>✅ 'distributor_id' column exists in customers table</p>";
    } else {
        echo "<p style='color: red;'>❌ 'distributor_id' column missing from customers table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking distributor_id column: " . $e->getMessage() . "</p>";
}

// Check user_id column in distributors table
try {
    $columns = DB::select("SHOW COLUMNS FROM distributors LIKE 'user_id'");
    if (count($columns) > 0) {
        echo "<p style='color: green;'>✅ 'user_id' column exists in distributors table</p>";
    } else {
        echo "<p style='color: red;'>❌ 'user_id' column missing from distributors table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking user_id column: " . $e->getMessage() . "</p>";
}

echo "<h2>Quick Fix Options</h2>";

echo "<p><strong>If you see issues above, run these scripts in order:</strong></p>";
echo "<ol>";
echo "<li><a href='fix-distributors-table.php?force=1' target='_blank'>Fix Distributors Table</a></li>";
echo "<li><a href='fix-customers-table.php?force=1' target='_blank'>Fix Customers Table</a></li>";
echo "<li><a href='fix-distributor-profiles.php?force=1' target='_blank'>Fix Distributor Profiles</a></li>";
echo "<li><a href='fix-customer-distributor-assignments.php?force=1' target='_blank'>Fix Customer-Distributor Assignments</a></li>";
echo "</ol>";

echo "<h2>Manual SQL Fix</h2>";

echo "<p><strong>If scripts don't work, run these SQL commands in phpMyAdmin:</strong></p>";

echo "<h3>1. Add distributor_id to customers table:</h3>";
echo "<pre>";
echo "ALTER TABLE customers ADD COLUMN distributor_id BIGINT UNSIGNED NULL AFTER id;\n";
echo "ALTER TABLE customers ADD CONSTRAINT fk_customers_distributor FOREIGN KEY (distributor_id) REFERENCES distributors(id);";
echo "</pre>";

echo "<h3>2. Add user_id to distributors table:</h3>";
echo "<pre>";
echo "ALTER TABLE distributors ADD COLUMN user_id BIGINT UNSIGNED NOT NULL AFTER id;\n";
echo "ALTER TABLE distributors ADD CONSTRAINT fk_distributors_user FOREIGN KEY (user_id) REFERENCES users(id);";
echo "</pre>";

echo "<h3>3. Assign customers to distributors:</h3>";
echo "<pre>";
echo "UPDATE customers SET distributor_id = 1 WHERE distributor_id IS NULL OR distributor_id = 0;";
echo "</pre>";

echo "<p><strong>After running the fixes, test the application again!</strong></p>";

?> 