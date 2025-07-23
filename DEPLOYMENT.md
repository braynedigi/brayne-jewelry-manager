# 🚀 Deployment Guide - Brayne Jewelry Manager

This guide will help you deploy your Brayne Jewelry Manager to cPanel or any shared hosting environment.

## 📋 Prerequisites

### cPanel Requirements
- **PHP 8.2 or higher**
- **MySQL 5.7 or higher** (or MariaDB 10.2+)
- **Composer** (if available)
- **File Manager** access
- **Database Manager** access

### Domain Setup
- **Domain name** pointing to your hosting
- **SSL certificate** (recommended for production)

## 🏗️ cPanel Deployment Steps

### Step 1: Database Setup

1. **Login to cPanel**
2. **Go to "MySQL Databases"**
3. **Create a new database:**
   - Database name: `brayne_jewelry` (or your preferred name)
   - Note down the full database name (usually includes your username)
4. **Create a database user:**
   - Username: `jewelry_user` (or your preferred name)
   - Strong password (save this!)
5. **Add user to database:**
   - Select your database and user
   - Grant "ALL PRIVILEGES"

### Step 2: File Upload

#### Option A: Using File Manager
1. **Go to "File Manager" in cPanel**
2. **Navigate to `public_html`** (or your domain's root directory)
3. **Create a new folder** called `jewelry-manager`
4. **Upload all project files** to this folder
5. **Extract if uploaded as ZIP**

#### Option B: Using FTP/SFTP
1. **Use FileZilla or similar FTP client**
2. **Connect to your hosting server**
3. **Upload all files** to `public_html/jewelry-manager/`

### Step 3: File Structure Setup

After upload, your structure should be:
```
public_html/
└── jewelry-manager/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── artisan
    └── composer.json
```

### Step 4: Environment Configuration

1. **Rename `.env.example` to `.env`**
2. **Edit `.env` file** with your production settings:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com/jewelry-manager

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_username_brayne_jewelry
DB_USERNAME=your_cpanel_username_jewelry_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Security Settings
SESSION_SECURE_COOKIE=true
PASSWORD_TIMEOUT=10800
LOGIN_THROTTLE=6
LOGIN_THROTTLE_DECAY=60
```

### Step 5: Application Setup

#### Option A: Using SSH (if available)
```bash
cd public_html/jewelry-manager
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Option B: Using cPanel Terminal (if available)
1. **Go to "Terminal" in cPanel**
2. **Navigate to your project directory**
3. **Run the commands above**

#### Option C: Manual Setup (if no terminal access)
1. **Generate APP_KEY manually** (use online Laravel key generator)
2. **Create storage link manually** (see below)
3. **Import database manually** (see below)

### Step 6: Database Migration

#### Option A: Using Artisan (if terminal available)
```bash
php artisan migrate:fresh --seed
```

#### Option B: Manual Database Import
1. **Go to "phpMyAdmin" in cPanel**
2. **Select your database**
3. **Import the SQL file** (you'll need to export from local first)
4. **Or run SQL commands manually** (see database/migrations/)

### Step 7: Storage Link Setup

#### Option A: Using Artisan
```bash
php artisan storage:link
```

#### Option B: Manual Setup
1. **In File Manager, go to `public_html/jewelry-manager/public/`**
2. **Create a symbolic link** from `storage` to `../storage/app/public`
3. **Or copy storage files** to public directory

### Step 8: Permissions Setup

Set proper file permissions:
- **Directories**: 755
- **Files**: 644
- **Storage directory**: 775
- **Bootstrap/cache**: 775

### Step 9: URL Configuration

#### Option A: Subdirectory Setup
If installed in `public_html/jewelry-manager/`:
- **Access URL**: `https://yourdomain.com/jewelry-manager`
- **No additional configuration needed**

#### Option B: Root Domain Setup
If you want it on the main domain:
1. **Move all files** from `jewelry-manager/` to `public_html/`
2. **Move contents** of `public/` to `public_html/`
3. **Update paths** in `index.php` and `.htaccess`

### Step 10: Final Configuration

1. **Test the application** by visiting your URL
2. **Login with default credentials:**
   - Admin: `admin@jewelry.com` / `password`
   - Distributor: `distributor1@jewelry.com` / `password`
   - Factory: `factory@jewelry.com` / `password`
3. **Change default passwords** immediately
4. **Update company settings** in admin panel

## 🔧 Troubleshooting

### Common Issues

#### 1. "500 Internal Server Error"
- Check file permissions
- Verify `.env` configuration
- Check error logs in cPanel

#### 2. "Database Connection Failed"
- Verify database credentials
- Check database host (usually `localhost`)
- Ensure database exists and user has permissions

#### 3. "Storage Link Not Working"
- Create manual symbolic link
- Or copy storage files to public directory

#### 4. "Composer Dependencies Missing"
- Upload `vendor/` folder
- Or run `composer install` via SSH

### Error Logs
- **cPanel Error Logs**: Check "Error Logs" in cPanel
- **Laravel Logs**: Check `storage/logs/laravel.log`
- **PHP Error Logs**: Check "PHP Error Log" in cPanel

## 🔒 Security Checklist

- [ ] **Change default passwords**
- [ ] **Set APP_DEBUG=false**
- [ ] **Use HTTPS**
- [ ] **Set proper file permissions**
- [ ] **Update admin email**
- [ ] **Configure backup system**
- [ ] **Set up SSL certificate**
- [ ] **Enable security headers**

## 📞 Support

If you encounter issues:
1. **Check error logs**
2. **Verify all steps completed**
3. **Contact your hosting provider**
4. **Create an issue on GitHub**

## 🚀 Post-Deployment

### Regular Maintenance
- **Backup database** regularly
- **Update Laravel** when new versions are released
- **Monitor error logs**
- **Check disk space**

### Performance Optimization
- **Enable OPcache** (if available)
- **Use CDN** for static assets
- **Optimize images**
- **Enable compression**

---

**Your Brayne Jewelry Manager is now ready for production! 🏪✨** 