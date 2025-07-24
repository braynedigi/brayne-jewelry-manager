# 🔧 cPanel Installation Troubleshooting Guide

## 🚨 Common Issues and Solutions

### Issue 1: 404 Not Found Errors

**Symptoms:**
- Installation says "complete" but you get 404 errors
- Cannot access dashboard or any pages
- URLs return "Page Not Found"

**Root Causes:**
1. Missing root `.htaccess` file
2. Incorrect document root configuration
3. URL rewriting not working
4. Missing vendor dependencies

**Solutions:**

#### Step 1: Check File Structure
Ensure your files are uploaded correctly:
```
public_html/
└── jewelry-manager/
    ├── .htaccess (NEW - root level)
    ├── .env
    ├── public/
    │   ├── .htaccess
    │   └── index.php
    ├── vendor/
    ├── storage/
    └── bootstrap/
```

#### Step 2: Verify Root .htaccess
The root `.htaccess` file is **CRITICAL** for subdirectory deployment:

```apache
# Laravel Root .htaccess for cPanel Subdirectory Deployment
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to the public directory
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Security: Prevent access to sensitive files
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
```

#### Step 3: Run the Fix Script
1. Upload `fix-cpanel-installation.php` to your jewelry-manager folder
2. Visit: `https://yourdomain.com/jewelry-manager/fix-cpanel-installation.php`
3. Follow the diagnostic results and fix commands

### Issue 2: Database Connection Errors

**Symptoms:**
- "Database connection failed" errors
- Cannot run migrations
- Application won't start

**Solutions:**

#### Check .env Configuration
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourusername_database_name
DB_USERNAME=yourusername_username
DB_PASSWORD=your_password
```

**Important:** In cPanel, database names and usernames are prefixed with your cPanel username.

#### Test Database Connection
```bash
php artisan tinker
DB::connection()->getPdo();
```

### Issue 3: Missing Dependencies

**Symptoms:**
- "Class not found" errors
- "Vendor autoload" errors
- Application won't start

**Solutions:**

#### Install Dependencies
```bash
cd public_html/jewelry-manager
composer install --optimize-autoloader --no-dev
```

#### If Composer Not Available
1. Download the `vendor` folder from your local development
2. Upload it to the jewelry-manager directory
3. Ensure all files are uploaded correctly

### Issue 4: Permission Errors

**Symptoms:**
- "Permission denied" errors
- Cannot write to storage
- Cache errors

**Solutions:**

#### Set Correct Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

#### Using File Manager
1. Select folders: `storage`, `bootstrap/cache`
2. Set permissions to `755`
3. Select files: `.env`, `composer.json`
4. Set permissions to `644`

### Issue 5: Application Key Issues

**Symptoms:**
- "Application key not set" errors
- Encryption errors
- Session issues

**Solutions:**

#### Generate Application Key
```bash
php artisan key:generate
```

#### Manual Key Generation
If terminal not available:
1. Go to: https://laravel-key-generator.com/
2. Generate a key
3. Add to `.env`: `APP_KEY=base64:your_generated_key`

### Issue 6: Storage Link Issues

**Symptoms:**
- Images not loading
- File upload errors
- "Storage link not found" errors

**Solutions:**

#### Create Storage Link
```bash
php artisan storage:link
```

#### Manual Storage Setup
1. In File Manager, go to `public_html/jewelry-manager/public/`
2. Create folder: `storage`
3. Copy contents from `storage/app/public/` to `public/storage/`

## 🛠️ Complete Fix Process

### Method 1: Using Terminal (Recommended)

```bash
# Navigate to project
cd public_html/jewelry-manager

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run migrations
php artisan migrate:fresh --seed

# Create storage link
php artisan storage:link

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Method 2: Manual Fix (No Terminal)

1. **Upload Missing Files**
   - Ensure `vendor/` folder is uploaded
   - Verify `.htaccess` files are in place
   - Check all Laravel files are present

2. **Configure .env**
   ```env
   APP_NAME="Brayne Jewelry Manager"
   APP_ENV=production
   APP_KEY=base64:your_generated_key
   APP_DEBUG=false
   APP_URL=https://yourdomain.com/jewelry-manager
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=yourusername_database
   DB_USERNAME=yourusername_user
   DB_PASSWORD=your_password
   ```

3. **Set File Permissions**
   - Folders: `755`
   - Files: `644`
   - Storage: `775`

4. **Create Storage Link Manually**
   - Copy `storage/app/public/` to `public/storage/`

## 🔍 Diagnostic Tools

### Use the Fix Script
1. Upload `fix-cpanel-installation.php`
2. Visit the script in your browser
3. Follow the diagnostic results

### Check Error Logs
- **cPanel Error Logs**: Check "Error Logs" in cPanel
- **Laravel Logs**: Check `storage/logs/laravel.log`
- **PHP Error Logs**: Check "PHP Error Log" in cPanel

### Test URLs
After fixes, test these URLs:
- `https://yourdomain.com/jewelry-manager/` (should redirect to login)
- `https://yourdomain.com/jewelry-manager/login` (login page)
- `https://yourdomain.com/jewelry-manager/admin/dashboard` (admin dashboard)

## 🔐 Default Login Credentials

After successful installation:
- **Admin**: `admin@jewelry.com` / `password`
- **Distributor**: `distributor1@jewelry.com` / `password`
- **Factory**: `factory@jewelry.com` / `password`

**⚠️ IMPORTANT:** Change these passwords immediately after first login!

## 📞 Getting Help

If you're still experiencing issues:

1. **Run the diagnostic script** (`fix-cpanel-installation.php`)
2. **Check error logs** in cPanel
3. **Verify file structure** matches the expected layout
4. **Contact your hosting provider** for mod_rewrite support
5. **Ensure PHP 8.2+** is enabled in cPanel

## ✅ Success Checklist

- [ ] Root `.htaccess` file exists
- [ ] Public `.htaccess` file exists
- [ ] `.env` file is configured correctly
- [ ] `vendor/` folder is uploaded
- [ ] Application key is generated
- [ ] Database connection works
- [ ] Storage link is created
- [ ] File permissions are correct
- [ ] Can access login page
- [ ] Can login with default credentials
- [ ] Dashboard loads without errors

---

**Remember:** The most common cause of 404 errors is the missing root `.htaccess` file. This file is essential for Laravel to work in a subdirectory on cPanel. 