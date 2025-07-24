<?php
/**
 * Debug User Access Script
 * 
 * This script helps diagnose why users are getting 403 Access Denied errors
 * when trying to access customer pages.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Debug User Access Script</h1>";

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
use App\Models\User;
use App\Models\Customer;
use App\Models\Distributor;

echo "<h2>Current Authentication Status</h2>";

if (Auth::check()) {
    $user = Auth::user();
    echo "<p style='color: green;'>✅ User is authenticated</p>";
    echo "<h3>User Information:</h3>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $user->id . "</li>";
    echo "<li><strong>Name:</strong> " . $user->name . "</li>";
    echo "<li><strong>Email:</strong> " . $user->email . "</li>";
    echo "<li><strong>Role:</strong> " . $user->role . "</li>";
    echo "</ul>";
    
    echo "<h3>Role Checks:</h3>";
    echo "<ul>";
    echo "<li><strong>isAdmin():</strong> " . ($user->isAdmin() ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li><strong>isDistributor():</strong> " . ($user->isDistributor() ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li><strong>isFactory():</strong> " . ($user->isFactory() ? '✅ Yes' : '❌ No') . "</li>";
    echo "</ul>";
    
    echo "<h3>Distributor Relationship:</h3>";
    if ($user->isDistributor()) {
        $distributor = $user->distributor;
        if ($distributor) {
            echo "<p style='color: green;'>✅ User has distributor profile</p>";
            echo "<ul>";
            echo "<li><strong>Distributor ID:</strong> " . $distributor->id . "</li>";
            echo "<li><strong>Company Name:</strong> " . $distributor->company_name . "</li>";
            echo "<li><strong>Phone:</strong> " . $distributor->phone . "</li>";
            echo "</ul>";
            
            // Check customers
            $customers = $distributor->customers;
            echo "<h3>Customer Access:</h3>";
            echo "<p><strong>Total customers for this distributor:</strong> " . $customers->count() . "</p>";
            
            if ($customers->count() > 0) {
                echo "<h4>Customer List:</h4>";
                echo "<ul>";
                foreach ($customers as $customer) {
                    echo "<li><strong>ID:</strong> " . $customer->id . " - <strong>Name:</strong> " . $customer->name . " - <strong>Email:</strong> " . $customer->email . "</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>❌ User is distributor but has no distributor profile!</p>";
            echo "<p>This is likely the cause of the 403 error.</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ User is not a distributor</p>";
    }
    
    echo "<h3>All Customers in Database:</h3>";
    $allCustomers = Customer::with('distributor.user')->get();
    echo "<p><strong>Total customers in database:</strong> " . $allCustomers->count() . "</p>";
    
    if ($allCustomers->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Distributor</th><th>Distributor User</th></tr>";
        foreach ($allCustomers as $customer) {
            $distributorName = $customer->distributor ? $customer->distributor->company_name : 'None';
            $distributorUser = $customer->distributor && $customer->distributor->user ? $customer->distributor->user->name : 'None';
            echo "<tr>";
            echo "<td>" . $customer->id . "</td>";
            echo "<td>" . $customer->name . "</td>";
            echo "<td>" . $customer->email . "</td>";
            echo "<td>" . $distributorName . "</td>";
            echo "<td>" . $distributorUser . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>Access Test Results:</h3>";
    
    // Test customer access logic
    if ($user->isAdmin()) {
        echo "<p style='color: green;'>✅ Admin can access all customers</p>";
    } elseif ($user->isDistributor()) {
        $distributor = $user->distributor;
        if (!$distributor) {
            echo "<p style='color: red;'>❌ Distributor has no profile - will get 403 error</p>";
            echo "<p><strong>Solution:</strong> Create distributor profile first</p>";
        } else {
            echo "<p style='color: green;'>✅ Distributor has profile - can access their customers</p>";
            echo "<p><strong>Customer count:</strong> " . $distributor->customers->count() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ User role not recognized - will get 403 error</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ User is not authenticated</p>";
    echo "<p>Please log in first.</p>";
}

echo "<hr>";
echo "<h2>Common Solutions</h2>";
echo "<ol>";
echo "<li><strong>If user is distributor but has no profile:</strong> Create distributor profile first</li>";
echo "<li><strong>If user role is incorrect:</strong> Update user role in database</li>";
echo "<li><strong>If customer has no distributor:</strong> Assign customer to distributor</li>";
echo "<li><strong>If distributor relationship is broken:</strong> Fix distributor relationship</li>";
echo "</ol>";

echo "<h3>Quick Fix Scripts:</h3>";
echo "<ul>";
echo "<li><a href='seed-default-users.php?force=1' target='_blank'>Create Default Users</a></li>";
echo "<li><a href='fix-customers-table.php?force=1' target='_blank'>Fix Customers Table</a></li>";
echo "</ul>";

echo "<h3>Manual Database Check:</h3>";
echo "<p>You can also check the database directly:</p>";
echo "<ul>";
echo "<li>Check <code>users</code> table for user role</li>";
echo "<li>Check <code>distributors</code> table for distributor profile</li>";
echo "<li>Check <code>customers</code> table for distributor_id</li>";
echo "</ul>";

?> 