# 🚀 cPanel Deployment Guide

## Quick Setup for Brayne Jewelry Manager

### Step 1: Database Setup
1. **Login to cPanel**
2. **Go to "MySQL Databases"**
3. **Create database**: `brayne_jewelry`
4. **Create user**: `jewelry_user` with strong password
5. **Add user to database** with ALL PRIVILEGES

### Step 2: Upload Files
1. **Go to "File Manager"**
2. **Navigate to `public_html`**
3. **Create folder**: `jewelry-manager`
4. **Upload all project files** to this folder

### Step 3: Environment Setup
1. **Rename `.env.example` to `.env`**
2. **Edit `.env` with your settings**:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com/jewelry-manager

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_username_brayne_jewelry
DB_USERNAME=your_username_jewelry_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Step 4: Generate App Key
1. **Go to "Terminal" in cPanel** (if available)
2. **Run**: `php artisan key:generate`
3. **Or use online generator**: https://laravel-key-generator.com/

### Step 5: Database Migration
**Option A: Terminal (if available)**
```bash
cd public_html/jewelry-manager
php artisan migrate:fresh --seed
```

**Option B: Manual Import**
1. **Export database** from your local environment
2. **Import via phpMyAdmin** in cPanel

### Step 6: Storage Setup
1. **Create folder**: `public_html/jewelry-manager/public/storage`
2. **Copy contents** from `storage/app/public/` to `public/storage/`

### Step 7: Permissions
Set permissions:
- **Folders**: 755
- **Files**: 644
- **Storage**: 775

### Step 8: Access Your Application
- **URL**: `https://yourdomain.com/jewelry-manager`
- **Default Login**: `admin@jewelry.com` / `password`

## 🔧 Troubleshooting

### 500 Error
- Check file permissions
- Verify `.env` configuration
- Check error logs in cPanel

### Database Error
- Verify database credentials
- Ensure database exists
- Check user permissions

### Storage Issues
- Create manual storage link
- Copy files to public directory

## 🔒 Security
- Change default passwords
- Set APP_DEBUG=false
- Use HTTPS
- Update admin email

Your jewelry manager is now live! 🏪✨ 