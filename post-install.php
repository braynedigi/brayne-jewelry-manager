<?php
/**
 * Post-Installation Setup Script
 * Run this after successful installation to configure Laravel properly
 */

// Check if .env exists
if (!file_exists('.env')) {
    die('Error: .env file not found. Please run the installer first.');
}

// Load environment variables
$env_content = file_get_contents('.env');
$lines = explode("\n", $env_content);
$env = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    
    $parts = explode('=', $line, 2);
    if (count($parts) == 2) {
        $env[trim($parts[0])] = trim($parts[1]);
    }
}

// Set environment variables
foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
    putenv("$key=$value");
}

// Bootstrap Laravel
require_once 'vendor/autoload.php';

// Create Laravel application
$app = require_once 'bootstrap/app.php';

// Set up storage link
try {
    if (!file_exists('public/storage')) {
        symlink('../storage/app/public', 'public/storage');
        echo "✅ Storage link created successfully\n";
    } else {
        echo "✅ Storage link already exists\n";
    }
} catch (Exception $e) {
    echo "⚠️  Warning: Could not create storage link: " . $e->getMessage() . "\n";
}

// Clear all caches
try {
    $app->make('Illuminate\Contracts\Console\Kernel')->call('config:clear');
    $app->make('Illuminate\Contracts\Console\Kernel')->call('cache:clear');
    $app->make('Illuminate\Contracts\Console\Kernel')->call('view:clear');
    $app->make('Illuminate\Contracts\Console\Kernel')->call('route:clear');
    echo "✅ All caches cleared successfully\n";
} catch (Exception $e) {
    echo "⚠️  Warning: Could not clear caches: " . $e->getMessage() . "\n";
}

// Optimize application
try {
    $app->make('Illuminate\Contracts\Console\Kernel')->call('config:cache');
    $app->make('Illuminate\Contracts\Console\Kernel')->call('route:cache');
    $app->make('Illuminate\Contracts\Console\Kernel')->call('view:cache');
    echo "✅ Application optimized successfully\n";
} catch (Exception $e) {
    echo "⚠️  Warning: Could not optimize application: " . $e->getMessage() . "\n";
}

// Set proper permissions
try {
    chmod('storage', 0755);
    chmod('bootstrap/cache', 0755);
    chmod('.env', 0644);
    echo "✅ File permissions set successfully\n";
} catch (Exception $e) {
    echo "⚠️  Warning: Could not set file permissions: " . $e->getMessage() . "\n";
}

echo "\n🎉 Post-installation setup completed!\n";
echo "You can now access your application at: " . ($env['APP_URL'] ?? 'your-domain.com') . "\n";
echo "Login with the admin credentials you set during installation.\n";
?> 