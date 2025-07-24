<?php
/**
 * Brayne Jewelry Manager - Test Runner
 * Run this script to execute all automated tests
 */

echo "🧪 Brayne Jewelry Manager - Test Runner\n";
echo "========================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from the Laravel project root directory\n";
    exit(1);
}

// Check if .env exists
if (!file_exists('.env')) {
    echo "❌ Error: .env file not found. Please run setup-application.php first\n";
    exit(1);
}

echo "1. Running PHPUnit tests...\n";
echo "---------------------------\n";

// Run the tests
$output = [];
$returnCode = 0;

exec('php artisan test --verbose', $output, $returnCode);

foreach ($output as $line) {
    echo $line . "\n";
}

if ($returnCode === 0) {
    echo "\n✅ All tests passed successfully!\n";
} else {
    echo "\n❌ Some tests failed. Please check the output above.\n";
}

echo "\n2. Running specific test suites...\n";
echo "--------------------------------\n";

// Run specific test suites
$testSuites = [
    'UserAuthenticationTest' => 'tests/Feature/UserAuthenticationTest.php',
    'OrderManagementTest' => 'tests/Feature/OrderManagementTest.php'
];

foreach ($testSuites as $suiteName => $testFile) {
    if (file_exists($testFile)) {
        echo "\nRunning $suiteName...\n";
        $output = [];
        exec("php artisan test $testFile --verbose", $output, $returnCode);
        
        foreach ($output as $line) {
            echo $line . "\n";
        }
        
        if ($returnCode === 0) {
            echo "✅ $suiteName passed\n";
        } else {
            echo "❌ $suiteName failed\n";
        }
    } else {
        echo "⚠️  $testFile not found\n";
    }
}

echo "\n3. Code coverage report...\n";
echo "-------------------------\n";

// Check if Xdebug is available for coverage
if (extension_loaded('xdebug')) {
    echo "Running code coverage analysis...\n";
    $output = [];
    exec('php artisan test --coverage --min=80', $output, $returnCode);
    
    foreach ($output as $line) {
        echo $line . "\n";
    }
} else {
    echo "⚠️  Xdebug not available. Install Xdebug for code coverage reports.\n";
}

echo "\n🎉 Test execution completed!\n";
echo "\nTest Results Summary:\n";
echo "- Unit Tests: Order Management, User Authentication\n";
echo "- Feature Tests: API endpoints, Business logic\n";
echo "- Integration Tests: Database operations, File uploads\n";
echo "\nTo run tests manually:\n";
echo "- All tests: php artisan test\n";
echo "- Specific test: php artisan test tests/Feature/OrderManagementTest.php\n";
echo "- With coverage: php artisan test --coverage\n";
?> 