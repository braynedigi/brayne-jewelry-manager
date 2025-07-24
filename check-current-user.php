<?php
/**
 * Check Current User Script
 * 
 * This script shows which distributor the current logged-in user is associated with.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Check Current User Script</h1>";

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

echo "<h2>Current User Information</h2>";

if (Auth::check()) {
    $user = Auth::user();
    echo "<p style='color: green;'>✅ User is authenticated</p>";
    echo "<p><strong>User ID:</strong> " . $user->id . "</p>";
    echo "<p><strong>Name:</strong> " . $user->name . "</p>";
    echo "<p><strong>Email:</strong> " . $user->email . "</p>";
    echo "<p><strong>Role:</strong> " . $user->role . "</p>";
    
    if ($user->role === 'distributor') {
        $distributor = $user->distributor;
        if ($distributor) {
            echo "<p style='color: green;'>✅ User has distributor profile</p>";
            echo "<p><strong>Distributor ID:</strong> " . $distributor->id . "</p>";
            echo "<p><strong>Company Name:</strong> " . $distributor->company_name . "</p>";
            
            // Check customers for this distributor
            $customers = Customer::where('distributor_id', $distributor->id)->get();
            echo "<p><strong>Customers for this distributor:</strong> " . $customers->count() . "</p>";
            
            if ($customers->count() > 0) {
                echo "<ul>";
                foreach ($customers as $customer) {
                    echo "<li>Customer ID: " . $customer->id . " - " . $customer->name . " (" . $customer->email . ")</li>";
                }
                echo "</ul>";
                echo "<p style='color: green; font-weight: bold;'>✅ This distributor has customers and should be able to create orders!</p>";
            } else {
                echo "<p style='color: red;'>❌ This distributor has no customers assigned</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ User does not have a distributor profile</p>";
            echo "<p>Run <a href='fix-distributor-profiles.php?force=1' target='_blank'>fix-distributor-profiles.php</a> to create the profile.</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ User is not a distributor (role: " . $user->role . ")</p>";
        echo "<p>Only distributors can create orders for customers.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ User is not authenticated</p>";
    echo "<p>Please log in first, then run this script again.</p>";
}

echo "<hr>";
echo "<h2>All Distributors</h2>";

$distributors = Distributor::with('user')->get();
echo "<p><strong>Total distributors:</strong> " . $distributors->count() . "</p>";

foreach ($distributors as $distributor) {
    $customers = Customer::where('distributor_id', $distributor->id)->count();
    echo "<p><strong>Distributor ID " . $distributor->id . ":</strong> " . $distributor->user->name . " (" . $distributor->company_name . ") - " . $customers . " customers</p>";
}

echo "<hr>";
echo "<h2>Login Credentials</h2>";
echo "<p>Try logging in with these accounts:</p>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@braynejewelry.com / password</li>";
echo "<li><strong>Distributor:</strong> distributor@braynejewelry.com / password</li>";
echo "<li><strong>Factory:</strong> factory@braynejewelry.com / password</li>";
echo "</ul>";

echo "<p><strong>After logging in, run this script again to see your distributor information.</strong></p>";

?> 