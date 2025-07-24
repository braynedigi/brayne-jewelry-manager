<?php
/**
 * Brayne Jewelry Manager - cPanel Package Creator
 * 
 * This script creates a deployment package for cPanel hosting
 */

echo "🏪 Brayne Jewelry Manager - cPanel Package Creator\n";
echo "==================================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from the Laravel project root directory\n";
    exit(1);
}

// Create production build
echo "1. Creating production build...\n";

// Optimize autoloader
echo "   - Optimizing autoloader...\n";
exec('composer install --optimize-autoloader --no-dev', $output, $returnCode);
if ($returnCode !== 0) {
    echo "   ❌ Failed to optimize autoloader\n";
    exit(1);
}

// Clear and cache config
echo "   - Caching configuration...\n";
exec('php artisan config:clear', $output, $returnCode);
exec('php artisan config:cache', $output, $returnCode);

// Clear and cache routes
echo "   - Caching routes...\n";
exec('php artisan route:clear', $output, $returnCode);
exec('php artisan route:cache', $output, $returnCode);

// Clear and cache views
echo "   - Caching views...\n";
exec('php artisan view:clear', $output, $returnCode);
exec('php artisan view:cache', $output, $returnCode);

echo "   ✅ Production build completed\n\n";

// Create deployment directory
echo "2. Creating deployment package...\n";
$deployDir = 'cpanel-deployment';
if (is_dir($deployDir)) {
    exec("rm -rf $deployDir");
}
mkdir($deployDir);

// Files and directories to include
$includeItems = [
    'app',
    'bootstrap',
    'config',
    'database',
    'lang',
    'public',
    'resources',
    'routes',
    'storage',
    'vendor',
    '.env.example',
    'artisan',
    'composer.json',
    'composer.lock'
];

// Copy files and directories
foreach ($includeItems as $item) {
    if (file_exists($item)) {
        if (is_dir($item)) {
            exec("cp -r $item $deployDir/");
        } else {
            copy($item, "$deployDir/$item");
        }
        echo "   ✅ Copied: $item\n";
    } else {
        echo "   ⚠️  Missing: $item\n";
    }
}

// Create root .htaccess file
echo "3. Creating root .htaccess file...\n";
$htaccessContent = '# Laravel Root .htaccess for cPanel
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to the public directory
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Prevent access to sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

<Files "artisan">
    Order allow,deny
    Deny from all
</Files>

# Prevent directory listing
Options -Indexes

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>';

file_put_contents("$deployDir/.htaccess", $htaccessContent);
echo "   ✅ Created root .htaccess file\n";

// Copy installation script
echo "4. Adding installation script...\n";
if (file_exists('cpanel-install.php')) {
    copy('cpanel-install.php', "$deployDir/cpanel-install.php");
    echo "   ✅ Added cpanel-install.php\n";
} else {
    echo "   ❌ cpanel-install.php not found\n";
}

// Create ZIP file
echo "5. Creating ZIP package...\n";
$zipFile = 'brayne-jewelry-manager-cpanel.zip';
if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($deployDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($deployDir) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();
    echo "   ✅ Created: $zipFile\n";
} else {
    echo "   ❌ Failed to create ZIP file\n";
}

// Clean up deployment directory
echo "6. Cleaning up...\n";
exec("rm -rf $deployDir");
echo "   ✅ Cleaned up temporary files\n";

echo "\n🎉 cPanel Deployment Package Created Successfully!\n";
echo "==================================================\n";
echo "📦 Package: $zipFile\n";
echo "📁 Size: " . number_format(filesize($zipFile) / 1024 / 1024, 2) . " MB\n\n";

echo "📋 Deployment Instructions:\n";
echo "1. Upload $zipFile to your cPanel public_html directory\n";
echo "2. Extract the ZIP file in a subdirectory (e.g., jewelry-manager)\n";
echo "3. Create a MySQL database in cPanel\n";
echo "4. Visit: https://yourdomain.com/jewelry-manager/cpanel-install.php\n";
echo "5. Follow the installation wizard\n";
echo "6. Delete cpanel-install.php after successful installation\n\n";

echo "📞 Support:\n";
echo "- Documentation: cpanel-deployment-guide.md\n";
echo "- Troubleshooting: CPANEL_TROUBLESHOOTING.md\n";
echo "- Email: support@braynedigital.com\n\n";

echo "✅ Your application is ready for cPanel deployment!\n";
?> 