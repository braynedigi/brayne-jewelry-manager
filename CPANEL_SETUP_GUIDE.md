# 🚀 Complete cPanel Deployment Guide - Brayne Jewelry Manager

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

---

## 🗂️ Step 1: Database Setup

### 1.1 Create Database
1. **Login to cPanel**
2. **Go to "MySQL Databases"**
3. **Create a new database:**
   - Database name: `brayne_jewelry` (or your preferred name)
   - Note down the full database name (usually includes your username)
   - Example: `yourusername_brayne_jewelry`

### 1.2 Create Database User
1. **In the same MySQL Databases section**
2. **Create a new user:**
   - Username: `jewelry_user` (or your preferred name)
   - Strong password (save this!)
   - Example: `yourusername_jewelry_user`

### 1.3 Add User to Database
1. **Scroll down to "Add User To Database"**
2. **Select your database and user**
3. **Grant "ALL PRIVILEGES"**
4. **Click "Add"**

---

## 📁 Step 2: File Upload

### 2.1 Using File Manager (Recommended)
1. **Go to "File Manager" in cPanel**
2. **Navigate to `public_html`**
3. **Create a new folder** called `jewelry-manager`
4. **Upload all project files** to this folder
5. **Extract if uploaded as ZIP**

### 2.2 Using FTP/SFTP
1. **Use FileZilla or similar FTP client**
2. **Connect to your hosting server**
3. **Upload all files** to `public_html/jewelry-manager/`

### 2.3 File Structure After Upload
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
    ├── composer.json
    └── deploy-cpanel.php
```

---

## ⚙️ Step 3: Environment Configuration

### 3.1 Create Environment File
1. **In File Manager, go to `jewelry-manager` folder**
2. **Rename `.env.example` to `.env`**
3. **Edit the `.env` file**

### 3.2 Configure Environment Variables
Replace the content with your production settings:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com/jewelry-manager

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourusername_brayne_jewelry
DB_USERNAME=yourusername_jewelry_user
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

**Important:** Replace `yourusername_brayne_jewelry` and `yourusername_jewelry_user` with your actual database names from Step 1.

---

## 🔧 Step 4: Application Setup

### 4.1 Using cPanel Terminal (Recommended)
1. **Go to "Terminal" in cPanel**
2. **Navigate to your project:**
   ```bash
   cd public_html/jewelry-manager
   ```

3. **Install dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations:**
   ```bash
   php artisan migrate:fresh
   ```

6. **Run database seeder:**
   ```bash
   php artisan db:seed
   ```

7. **Create storage link:**
   ```bash
   php artisan storage:link
   ```

8. **Cache configurations:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### 4.2 Using Deployment Script
1. **Upload the `deploy-cpanel.php` file**
2. **Visit**: `https://yourdomain.com/jewelry-manager/deploy-cpanel.php`
3. **Follow the on-screen instructions**

### 4.3 Manual Setup (if no terminal access)
1. **Generate APP_KEY manually** (use online Laravel key generator)
2. **Create storage link manually** (see Step 5)
3. **Import database manually** (see Step 6)

---

## 📁 Step 5: Storage Setup

### 5.1 Using Artisan (if terminal available)
```bash
php artisan storage:link
```

### 5.2 Manual Storage Setup
1. **In File Manager, go to `public_html/jewelry-manager/public/`**
2. **Create a symbolic link** from `storage` to `../storage/app/public`
3. **Or copy storage files** to public directory

### 5.3 Alternative Manual Method
1. **Create folder**: `public_html/jewelry-manager/public/storage`
2. **Copy contents** from `storage/app/public/` to `public/storage/`

---

## 🗄️ Step 6: Database Migration

### 6.1 Using Artisan (if terminal available)
```bash
php artisan migrate:fresh --seed
```

### 6.2 Manual Database Import
1. **Go to "phpMyAdmin" in cPanel**
2. **Select your database**
3. **Import the SQL file** (you'll need to export from local first)
4. **Or run SQL commands manually** (see database/migrations/)

---

## 🔐 Step 7: Set File Permissions

Set these permissions in File Manager:
- **Directories**: 755
- **Files**: 644
- **Storage directory**: 775
- **Bootstrap/cache**: 775

### 7.1 Using File Manager
1. **Select folders/files**
2. **Click "Permissions"**
3. **Set appropriate permissions**

### 7.2 Using Terminal
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## 🧪 Step 8: Test Your Application

### 8.1 Access Your Application
- **Main URL**: `https://yourdomain.com/jewelry-manager`
- **Login URL**: `https://yourdomain.com/jewelry-manager/login`

### 8.2 Default Login Credentials
- **Admin**: `admin@jewelry.com` / `password`
- **Distributor**: `distributor1@jewelry.com` / `password`
- **Factory**: `factory@jewelry.com` / `password`

### 8.3 Test All Features
1. **Login with each user type**
2. **Test order creation**
3. **Test file uploads**
4. **Test notifications**

---

## 🔒 Step 9: Security Setup

### 9.1 Change Default Passwords
1. **Login as admin**
2. **Go to Users section**
3. **Change all default passwords**

### 9.2 Update Settings
1. **Go to Admin → Settings**
2. **Update company information**
3. **Configure email settings**

### 9.3 SSL Certificate
1. **Enable SSL in cPanel**
2. **Force HTTPS redirects**

---

## 🚨 Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check file permissions (755 for folders, 644 for files)
- Verify `.env` configuration
- Check error logs in cPanel

#### Database Connection Failed
- Verify database credentials in `.env`
- Ensure database exists and user has permissions
- Check database host (usually `localhost`)

#### Storage/File Upload Issues
- Create manual storage link
- Set storage directory permissions to 775
- Copy storage files to public directory if needed

#### Composer Dependencies Missing
- Upload `vendor/` folder from your local project
- Or run `composer install` via SSH/Terminal

### Error Logs
- **cPanel Error Logs**: Check "Error Logs" in cPanel
- **Laravel Logs**: Check `storage/logs/laravel.log`
- **PHP Error Logs**: Check "PHP Error Log" in cPanel

---

## ✅ Final Checklist

- [ ] Database created and configured
- [ ] Files uploaded to correct directory
- [ ] `.env` file configured with production settings
- [ ] Dependencies installed
- [ ] Application key generated
- [ ] Database migrated and seeded
- [ ] Storage link created
- [ ] File permissions set correctly
- [ ] Application accessible via URL
- [ ] All user types can login
- [ ] Default passwords changed
- [ ] SSL certificate enabled

---

## 🎉 Success!

Your Brayne Jewelry Manager is now live at:
**`https://yourdomain.com/jewelry-manager`**

### Features Available:
- ✅ Complete user management system
- ✅ Order management with templates
- ✅ Real-time notifications
- ✅ Role-based access control
- ✅ Production-ready security

### Next Steps:
1. Test all functionality
2. Change default passwords
3. Configure email settings
4. Set up regular backups
5. Monitor performance

---

**Need Help?** Check the error logs or contact your hosting provider for assistance. 