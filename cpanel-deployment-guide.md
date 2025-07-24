# 🏪 Brayne Jewelry Manager - cPanel Deployment Guide

## 🚨 **Hosting Company Issues - Complete Solution**

### **Step 1: Prepare Your Application for cPanel**

#### **1.1 Create Production Build**
```bash
# In your local development environment
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### **1.2 Create cPanel Deployment Package**
Create a ZIP file with these files only:
```
jewelry-manager/
├── app/
├── bootstrap/
├── config/
├── database/
├── lang/
├── public/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env.example
├── .htaccess (ROOT LEVEL - CRITICAL)
├── artisan
├── composer.json
└── composer.lock
```

### **Step 2: cPanel Upload and Setup**

#### **2.1 Upload to cPanel**
1. **Upload** the ZIP file to your cPanel `public_html` folder
2. **Extract** the files in a subdirectory (e.g., `jewelry-manager`)
3. **Set permissions**:
   ```bash
   chmod 755 storage/
   chmod 755 bootstrap/cache/
   chmod 644 .env
   ```

#### **2.2 Create Root .htaccess File**
**CRITICAL**: Create this `.htaccess` file in your `jewelry-manager` root directory:

```apache
# Laravel Root .htaccess for cPanel
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
</IfModule>
```

### **Step 3: Database Configuration**

#### **3.1 Create Database in cPanel**
1. Go to **cPanel > MySQL Databases**
2. Create a new database: `yourusername_jewelry_db`
3. Create a new user: `yourusername_jewelry_user`
4. Add user to database with **ALL PRIVILEGES**

#### **3.2 Configure .env File**
Create `.env` file in your `jewelry-manager` directory:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com/jewelry-manager

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourusername_jewelry_db
DB_USERNAME=yourusername_jewelry_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### **Step 4: Run Installation Script**

#### **4.1 Upload Installation Script**
Upload `cpanel-install.php` to your `jewelry-manager` directory:

```php
<?php
/**
 * Brayne Jewelry Manager - cPanel Installation Script
 */

echo "<h1>🏪 Brayne Jewelry Manager - cPanel Installation</h1>";

// Step 1: Generate App Key
echo "<h2>Step 1: Generating Application Key</h2>";
$appKey = 'base64:' . base64_encode(random_bytes(32));
echo "✅ Application key generated: " . substr($appKey, 0, 20) . "...<br>";

// Step 2: Update .env file
echo "<h2>Step 2: Updating .env Configuration</h2>";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $appKey, $envContent);
    file_put_contents('.env', $envContent);
    echo "✅ .env file updated with new app key<br>";
} else {
    echo "❌ .env file not found<br>";
}

// Step 3: Create storage directories
echo "<h2>Step 3: Creating Storage Directories</h2>";
$directories = [
    'storage/app/public',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created: $dir<br>";
    } else {
        echo "✅ Exists: $dir<br>";
    }
}

// Step 4: Set permissions
echo "<h2>Step 4: Setting Permissions</h2>";
chmod('storage', 0755);
chmod('bootstrap/cache', 0755);
echo "✅ Permissions set<br>";

// Step 5: Run migrations
echo "<h2>Step 5: Running Database Migrations</h2>";
$output = [];
exec('php artisan migrate --force', $output, $returnCode);
foreach ($output as $line) {
    echo $line . "<br>";
}

if ($returnCode === 0) {
    echo "✅ Migrations completed successfully<br>";
} else {
    echo "❌ Migration failed<br>";
}

// Step 6: Seed database
echo "<h2>Step 6: Seeding Database</h2>";
$output = [];
exec('php artisan db:seed --force', $output, $returnCode);
foreach ($output as $line) {
    echo $line . "<br>";
}

if ($returnCode === 0) {
    echo "✅ Database seeded successfully<br>";
} else {
    echo "❌ Seeding failed<br>";
}

echo "<h2>🎉 Installation Complete!</h2>";
echo "<p>Your Brayne Jewelry Manager is now ready to use.</p>";
echo "<p><strong>Access URL:</strong> https://yourdomain.com/jewelry-manager</p>";
echo "<p><strong>Default Login:</strong></p>";
echo "<ul>";
echo "<li>Admin: admin@jewelry.com / password</li>";
echo "<li>Distributor: distributor1@jewelry.com / password</li>";
echo "<li>Factory: factory@jewelry.com / password</li>";
echo "</ul>";
?>
```

### **Step 5: Test and Verify**

#### **5.1 Access Your Application**
Visit: `https://yourdomain.com/jewelry-manager`

#### **5.2 Common Issues and Solutions**

**Issue: 404 Not Found**
- **Solution**: Ensure root `.htaccess` file exists and is correct
- **Check**: File permissions (755 for directories, 644 for files)

**Issue: Database Connection Failed**
- **Solution**: Verify database credentials in `.env`
- **Check**: Database name and username have cPanel prefix

**Issue: Permission Denied**
- **Solution**: Set correct file permissions
- **Commands**:
  ```bash
  chmod 755 storage/
  chmod 755 bootstrap/cache/
  chmod 644 .env
  ```

**Issue: White Screen**
- **Solution**: Check error logs in cPanel
- **Enable**: Debug mode temporarily in `.env` (APP_DEBUG=true)

### **Step 6: Security and Optimization**

#### **6.1 Security Measures**
1. **Remove installation files** after successful setup
2. **Set APP_DEBUG=false** in production
3. **Use HTTPS** for all connections
4. **Regular backups** of database and files

#### **6.2 Performance Optimization**
1. **Enable caching**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Optimize autoloader**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

### **Step 7: Support Information**

#### **For Hosting Company:**
- **PHP Requirements**: 8.2 or higher
- **Extensions**: pdo, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json
- **Memory Limit**: 256MB minimum
- **Upload Limit**: 64MB minimum
- **Execution Time**: 300 seconds minimum

#### **Contact Information:**
- **Technical Support**: support@braynedigital.com
- **Documentation**: https://docs.braynejewelry.com
- **GitHub**: https://github.com/brayne/jewelry-manager

---

## 🎯 **Quick Deployment Checklist**

- [ ] Upload application files to cPanel
- [ ] Create root `.htaccess` file
- [ ] Create database and user in cPanel
- [ ] Configure `.env` file with database credentials
- [ ] Run `cpanel-install.php` script
- [ ] Test application access
- [ ] Remove installation files
- [ ] Set production environment variables
- [ ] Enable caching for performance
- [ ] Configure SSL certificate

---

**🏪 Brayne Jewelry Manager - Production Ready for cPanel**  
**Version**: 1.0.0  
**Last Updated**: July 24, 2025 