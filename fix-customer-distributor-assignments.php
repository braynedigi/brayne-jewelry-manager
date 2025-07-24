<?php
/**
 * Fix Customer-Distributor Assignments Script
 * 
 * This script assigns customers to distributors so distributors can see
 * and create orders for their own customers.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Customer-Distributor Assignments Script</h1>";

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

echo "<h2>Fixing Customer-Distributor Assignments</h2>";

// Get all distributors
$distributors = Distributor::with('user')->get();

echo "<p><strong>Total distributors found:</strong> " . $distributors->count() . "</p>";

if ($distributors->count() == 0) {
    echo "<p style='color: orange;'>⚠ No distributors found!</p>";
    echo "<p>Please run <a href='fix-distributor-profiles.php?force=1' target='_blank'>fix-distributor-profiles.php</a> first.</p>";
    exit;
}

// Get all customers
$customers = Customer::all();

echo "<p><strong>Total customers found:</strong> " . $customers->count() . "</p>";

if ($customers->count() == 0) {
    echo "<p style='color: orange;'>⚠ No customers found!</p>";
    echo "<p>Please run <a href='fix-customers-table.php?force=1' target='_blank'>fix-customers-table.php</a> first.</p>";
    exit;
}

$assignedCustomers = 0;
$createdCustomers = 0;

foreach ($distributors as $distributor) {
    echo "<h3>Processing distributor: " . $distributor->user->name . " (ID: " . $distributor->id . ")</h3>";
    
    // Check if distributor already has customers
    $existingCustomers = Customer::where('distributor_id', $distributor->id)->count();
    
    if ($existingCustomers > 0) {
        echo "<p style='color: green;'>✅ Distributor already has $existingCustomers customers</p>";
        $assignedCustomers += $existingCustomers;
    } else {
        echo "<p style='color: orange;'>⚠ Distributor has no customers - creating sample customers...</p>";
        
        // Create sample customers for this distributor
        $sampleCustomers = [
            [
                'name' => 'John Customer',
                'email' => 'john.customer@example.com',
                'phone' => '+1234567890',
                'street' => '123 Main St',
                'barangay' => 'Downtown',
                'city' => 'Manila',
                'province' => 'Metro Manila',
                'country' => 'Philippines'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1234567891',
                'street' => '456 Oak Ave',
                'barangay' => 'Uptown',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'country' => 'Philippines'
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'phone' => '+1234567892',
                'street' => '789 Pine Rd',
                'barangay' => 'Business District',
                'city' => 'Makati',
                'province' => 'Metro Manila',
                'country' => 'Philippines'
            ]
        ];
        
        foreach ($sampleCustomers as $customerData) {
            try {
                $customer = Customer::create([
                    'distributor_id' => $distributor->id,
                    'name' => $customerData['name'],
                    'email' => $customerData['email'],
                    'phone' => $customerData['phone'],
                    'street' => $customerData['street'],
                    'barangay' => $customerData['barangay'],
                    'city' => $customerData['city'],
                    'province' => $customerData['province'],
                    'country' => $customerData['country']
                ]);
                
                echo "<p style='color: green;'>✅ Created customer: " . $customer->name . "</p>";
                $createdCustomers++;
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Failed to create customer " . $customerData['name'] . ": " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<hr>";
}

// Assign unassigned customers to distributors
$unassignedCustomers = Customer::whereNull('distributor_id')->orWhere('distributor_id', 0)->get();

if ($unassignedCustomers->count() > 0) {
    echo "<h3>Assigning unassigned customers to distributors...</h3>";
    
    $distributorIds = $distributors->pluck('id')->toArray();
    $distributorIndex = 0;
    
    foreach ($unassignedCustomers as $customer) {
        $distributorId = $distributorIds[$distributorIndex % count($distributorIds)];
        
        try {
            $customer->update(['distributor_id' => $distributorId]);
            echo "<p style='color: green;'>✅ Assigned customer " . $customer->name . " to distributor ID: $distributorId</p>";
            $assignedCustomers++;
            $distributorIndex++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Failed to assign customer " . $customer->name . ": " . $e->getMessage() . "</p>";
        }
    }
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p style='color: green; font-weight: bold;'>🎉 Customer-Distributor assignments completed!</p>";
echo "<ul>";
echo "<li><strong>Distributors processed:</strong> " . $distributors->count() . "</li>";
echo "<li><strong>Customers assigned:</strong> " . $assignedCustomers . "</li>";
echo "<li><strong>New customers created:</strong> " . $createdCustomers . "</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Log in as a distributor</strong></li>";
echo "<li><strong>Try creating an order:</strong> Visit <a href='/orders/create' target='_blank'>create order page</a></li>";
echo "<li><strong>Check customer dropdown:</strong> Should now show customers for this distributor</li>";
echo "<li><strong>Test customer access:</strong> Visit <a href='/customers' target='_blank'>customers page</a></li>";
echo "</ol>";

echo "<h3>Login Credentials:</h3>";
echo "<p>If you need to test with different users:</p>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@braynejewelry.com / password</li>";
echo "<li><strong>Distributor:</strong> distributor@braynejewelry.com / password</li>";
echo "<li><strong>Factory:</strong> factory@braynejewelry.com / password</li>";
echo "</ul>";

echo "<p><strong>The 'You can only create orders for your own customers' error should now be fixed!</strong></p>";

?> 