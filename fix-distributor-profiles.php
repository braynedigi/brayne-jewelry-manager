<?php
/**
 * Fix Distributor Profiles Script
 * 
 * This script creates missing distributor profiles for users with distributor role.
 * This fixes the 403 Access Denied error when distributors try to access customers.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Distributor Profiles Script</h1>";

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
use App\Models\Distributor;

echo "<h2>Checking Distributor Profiles</h2>";

// Get all users with distributor role
$distributorUsers = User::where('role', 'distributor')->get();

echo "<p><strong>Total users with distributor role:</strong> " . $distributorUsers->count() . "</p>";

if ($distributorUsers->count() == 0) {
    echo "<p style='color: orange;'>⚠ No users with distributor role found!</p>";
    echo "<p>You may need to create distributor users first.</p>";
    echo "<p><a href='seed-default-users.php?force=1' target='_blank'>Create Default Users</a></p>";
    exit;
}

$fixedProfiles = 0;
$existingProfiles = 0;

foreach ($distributorUsers as $user) {
    echo "<h3>Checking user: " . $user->name . " (ID: " . $user->id . ")</h3>";
    
    // Check if user already has a distributor profile
    $existingProfile = Distributor::where('user_id', $user->id)->first();
    
    if ($existingProfile) {
        echo "<p style='color: green;'>✅ User already has distributor profile</p>";
        echo "<ul>";
        echo "<li><strong>Company Name:</strong> " . $existingProfile->company_name . "</li>";
        echo "<li><strong>Phone:</strong> " . $existingProfile->phone . "</li>";
        echo "<li><strong>Address:</strong> " . $existingProfile->street . ", " . $existingProfile->city . "</li>";
        echo "</ul>";
        $existingProfiles++;
    } else {
        echo "<p style='color: red;'>❌ User missing distributor profile - creating one...</p>";
        
        try {
            // Create distributor profile
            $distributor = Distributor::create([
                'user_id' => $user->id,
                'company_name' => $user->name . ' Company',
                'phone' => '+1234567890',
                'street' => '123 Business St',
                'city' => 'Business City',
                'province' => 'Business Province',
                'country' => 'Philippines',
                'is_international' => false,
            ]);
            
            echo "<p style='color: green;'>✅ Created distributor profile</p>";
            echo "<ul>";
            echo "<li><strong>Company Name:</strong> " . $distributor->company_name . "</li>";
            echo "<li><strong>Phone:</strong> " . $distributor->phone . "</li>";
            echo "<li><strong>Address:</strong> " . $distributor->street . ", " . $distributor->city . "</li>";
            echo "</ul>";
            $fixedProfiles++;
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Failed to create distributor profile: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
}

echo "<h2>Summary</h2>";
echo "<p style='color: green; font-weight: bold;'>🎉 Distributor profiles check completed!</p>";
echo "<ul>";
echo "<li><strong>Users checked:</strong> " . $distributorUsers->count() . "</li>";
echo "<li><strong>Existing profiles:</strong> " . $existingProfiles . "</li>";
echo "<li><strong>Profiles created:</strong> " . $fixedProfiles . "</li>";
echo "</ul>";

if ($fixedProfiles > 0) {
    echo "<p style='color: green;'>✅ The 403 Access Denied error should now be fixed!</p>";
    echo "<p>Distributors can now access their customer pages.</p>";
} else {
    echo "<p style='color: blue;'>ℹ All distributor profiles were already in place.</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test customer access:</strong> Try accessing <a href='/customers' target='_blank'>customers page</a></li>";
echo "<li><strong>Test customer creation:</strong> Try creating a new customer</li>";
echo "<li><strong>If still having issues:</strong> Run <a href='debug-user-access.php?force=1' target='_blank'>debug script</a></li>";
echo "</ol>";

echo "<h3>Login Credentials:</h3>";
echo "<p>If you need to test with different users:</p>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@braynejewelry.com / password</li>";
echo "<li><strong>Distributor:</strong> distributor@braynejewelry.com / password</li>";
echo "<li><strong>Factory:</strong> factory@braynejewelry.com / password</li>";
echo "</ul>";

?> 