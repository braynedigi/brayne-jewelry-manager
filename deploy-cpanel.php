<?php
/**
 * Brayne Jewelry Manager - cPanel Deployment Script
 * 
 * This script helps set up the application on cPanel hosting
 * Run this file in your browser after uploading to cPanel
 */

// Check if running in web environment
if (php_sapi_name() !== 'cli') {
    echo "<h1>🏪 Brayne Jewelry Manager - cPanel Setup</h1>";
    echo "<style>body{font-family:Arial,sans-serif;margin:40px;line-height:1.6;} .step{background:#f5f5f5;padding:20px;margin:20px 0;border-radius:5px;} .error{color:red;} .success{color:green;} .warning{color:orange;}</style>";
}

// Function to display messages
function showMessage($message, $type = 'info') {
    if (php_sapi_name() === 'cli') {
        echo "[$type] $message\n";
    } else {
        $color = $type === 'error' ? 'red' : ($type === 'success' ? 'green' : 'blue');
        echo "<div style='color:$color;'>$message</div>";
    }
}

// Check PHP version
if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    showMessage("❌ PHP 8.2 or higher required. Current version: " . PHP_VERSION, 'error');
    exit;
} else {
    showMessage("✅ PHP version: " . PHP_VERSION, 'success');
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    showMessage("❌ Missing PHP extensions: " . implode(', ', $missing_extensions), 'error');
    exit;
} else {
    showMessage("✅ All required PHP extensions are available", 'success');
}

// Check if .env exists
if (!file_exists('.env')) {
    showMessage("⚠️  .env file not found. Please create it from .env.example", 'warning');
} else {
    showMessage("✅ .env file exists", 'success');
}

// Check if vendor directory exists
if (!is_dir('vendor')) {
    showMessage("⚠️  Vendor directory not found. Please run: composer install", 'warning');
} else {
    showMessage("✅ Vendor directory exists", 'success');
}

// Check storage directory permissions
$storage_path = 'storage';
if (!is_writable($storage_path)) {
    showMessage("⚠️  Storage directory is not writable. Please set permissions to 775", 'warning');
} else {
    showMessage("✅ Storage directory is writable", 'success');
}

// Check bootstrap/cache directory permissions
$cache_path = 'bootstrap/cache';
if (!is_writable($cache_path)) {
    showMessage("⚠️  Bootstrap/cache directory is not writable. Please set permissions to 775", 'warning');
} else {
    showMessage("✅ Bootstrap/cache directory is writable", 'success');
}

// Display setup instructions
if (php_sapi_name() !== 'cli') {
    echo "<div class='step'>";
    echo "<h2>📋 Setup Instructions</h2>";
    echo "<ol>";
    echo "<li><strong>Database Setup:</strong> Create MySQL database and user in cPanel</li>";
    echo "<li><strong>Environment:</strong> Configure .env file with database credentials</li>";
    echo "<li><strong>Dependencies:</strong> Run: composer install --optimize-autoloader --no-dev</li>";
    echo "<li><strong>App Key:</strong> Run: php artisan key:generate</li>";
    echo "<li><strong>Database:</strong> Run: php artisan migrate:fresh --seed</li>";
    echo "<li><strong>Storage:</strong> Run: php artisan storage:link</li>";
    echo "<li><strong>Cache:</strong> Run: php artisan config:cache && php artisan route:cache && php artisan view:cache</li>";
    echo "</ol>";
    echo "</div>";

    echo "<div class='step'>";
    echo "<h2>🔧 Quick Commands (if Terminal available)</h2>";
    echo "<pre>";
    echo "cd public_html/jewelry-manager\n";
    echo "composer install --optimize-autoloader --no-dev\n";
    echo "php artisan key:generate\n";
    echo "php artisan migrate:fresh --seed\n";
    echo "php artisan storage:link\n";
    echo "php artisan config:cache\n";
    echo "php artisan route:cache\n";
    echo "php artisan view:cache\n";
    echo "</pre>";
    echo "</div>";

    echo "<div class='step'>";
    echo "<h2>🔐 Default Login Credentials</h2>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@jewelry.com / password</li>";
    echo "<li><strong>Distributor:</strong> distributor1@jewelry.com / password</li>";
    echo "<li><strong>Factory:</strong> factory@jewelry.com / password</li>";
    echo "</ul>";
    echo "<p><strong>⚠️  IMPORTANT:</strong> Change these passwords immediately after setup!</p>";
    echo "</div>";

    echo "<div class='step'>";
    echo "<h2>🌐 Access Your Application</h2>";
    echo "<p>After setup, access your application at:</p>";
    echo "<code>https://yourdomain.com/jewelry-manager</code>";
    echo "</div>";
}

showMessage("🎉 Setup check completed! Follow the instructions above to complete deployment.", 'success');
?> 