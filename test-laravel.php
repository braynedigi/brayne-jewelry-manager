<?php
/**
 * Laravel Test File
 * This file helps diagnose Laravel configuration issues
 */

echo "<h1>Laravel Configuration Test</h1>";

// Check if .env exists
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file missing<br>";
}

// Check if vendor directory exists
if (is_dir('vendor')) {
    echo "✅ Vendor directory exists<br>";
} else {
    echo "❌ Vendor directory missing<br>";
}

// Check if bootstrap/app.php exists
if (file_exists('bootstrap/app.php')) {
    echo "✅ bootstrap/app.php exists<br>";
} else {
    echo "❌ bootstrap/app.php missing<br>";
}

// Check if storage directory is writable
if (is_writable('storage')) {
    echo "✅ Storage directory is writable<br>";
} else {
    echo "❌ Storage directory is not writable<br>";
}

// Check if bootstrap/cache directory is writable
if (is_writable('bootstrap/cache')) {
    echo "✅ Bootstrap cache directory is writable<br>";
} else {
    echo "❌ Bootstrap cache directory is not writable<br>";
}

// Try to load Laravel
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel application loaded successfully<br>";
    
    // Test database connection
    try {
        $db = $app->make('Illuminate\Database\Connection');
        $db->getPdo();
        echo "✅ Database connection successful<br>";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Failed to load Laravel: " . $e->getMessage() . "<br>";
}

// Check .htaccess
if (file_exists('public/.htaccess')) {
    echo "✅ .htaccess file exists<br>";
} else {
    echo "❌ .htaccess file missing<br>";
}

echo "<br><strong>If you see any ❌ marks, those issues need to be resolved before Laravel will work properly.</strong>";
?> 