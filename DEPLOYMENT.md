# 🚀 Deployment Guide - Brayne Jewelry Manager

This guide will help you deploy your Brayne Jewelry Manager using Docker.

## 📋 Prerequisites

### Docker Requirements
- **Docker Desktop** installed and running
- **Docker Compose** (included with Docker Desktop)
- **Git** (for version control)

### Domain Setup
- **Domain name** pointing to your server
- **SSL certificate** (recommended for production)

## 🐳 Docker Deployment Steps

### Step 1: Database Setup

The database will be automatically created when you run Docker containers. No manual setup required.

### Step 2: Environment Configuration

1. **Copy `.env.example` to `.env`**
2. **Edit `.env` file** with your production settings:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=jewelry_new
DB_USERNAME=sail
DB_PASSWORD=password

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
```

### Step 3: Docker Setup

1. **Start Docker Desktop**
2. **Run the application:**
   ```bash
   docker-compose up -d
   ```

### Step 4: Application Setup

1. **Generate application key:**
   ```bash
   docker-compose exec laravel.test php artisan key:generate
   ```

2. **Run database migrations:**
   ```bash
   docker-compose exec laravel.test php artisan migrate
   ```

3. **Set proper permissions:**
   ```bash
   docker-compose exec laravel.test chmod -R 775 storage bootstrap/cache
   ```

### Step 5: Production Optimization

1. **Optimize for production:**
   ```bash
   docker-compose exec laravel.test php artisan config:cache
   docker-compose exec laravel.test php artisan route:cache
   docker-compose exec laravel.test php artisan view:cache
   ```

2. **Build frontend assets:**
   ```bash
   docker-compose exec laravel.test npm run build
   ```

## 🚀 Production Deployment

### Using Docker Compose

1. **Start services:**
   ```bash
   docker-compose up -d
   ```

2. **Check status:**
   ```bash
   docker-compose ps
   ```

3. **View logs:**
   ```bash
   docker-compose logs
   ```

### Using Docker Swarm (for production)

1. **Initialize swarm:**
   ```bash
   docker swarm init
   ```

2. **Deploy stack:**
   ```bash
   docker stack deploy -c docker-compose.yml jewelry-manager
   ```

## 🔧 Maintenance Commands

### Database Operations
```bash
# Run migrations
docker-compose exec laravel.test php artisan migrate

# Seed database
docker-compose exec laravel.test php artisan db:seed

# Backup database
docker-compose exec mysql mysqldump -u sail -ppassword jewelry_new > backup.sql
```

### Application Commands
```bash
# Clear caches
docker-compose exec laravel.test php artisan cache:clear
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan route:clear

# View logs
docker-compose exec laravel.test php artisan tail

# Access tinker
docker-compose exec laravel.test php artisan tinker
```

### Container Management
```bash
# Stop services
docker-compose down

# Restart services
docker-compose restart

# Update containers
docker-compose pull
docker-compose up -d
```

## 🔒 Security Considerations

1. **Change default passwords** in production
2. **Use environment variables** for sensitive data
3. **Enable SSL/TLS** for HTTPS
4. **Regular security updates** for containers
5. **Backup strategy** for database and files

## 📊 Monitoring

### Health Checks
```bash
# Check application health
curl http://localhost/health

# Check database connection
docker-compose exec laravel.test php artisan tinker --execute="DB::connection()->getPdo();"
```

### Logs
```bash
# Application logs
docker-compose logs laravel.test

# Database logs
docker-compose logs mysql

# All logs
docker-compose logs -f
```

## 🆘 Troubleshooting

### Common Issues

1. **Port conflicts:**
   - Check if ports 80, 3306 are available
   - Modify ports in docker-compose.yml if needed

2. **Permission issues:**
   ```bash
   docker-compose exec laravel.test chmod -R 775 storage bootstrap/cache
   ```

3. **Database connection:**
   - Verify MySQL container is running
   - Check database credentials in .env

4. **Application not loading:**
   - Check if all containers are running
   - Verify APP_URL in .env matches your domain

### Support

- Check Docker Desktop is running
- Verify all containers are up: `docker-compose ps`
- Check logs: `docker-compose logs [service-name]`
- Ensure `.env` file has correct configuration

## 🎉 Success!

Your Brayne Jewelry Manager is now running with Docker! 

- **Application**: http://localhost (or your domain)
- **Database**: Accessible via Docker container
- **Logs**: Available via `docker-compose logs`

For development, see the `DOCKER_MIGRATION_GUIDE.md` file for detailed setup instructions. 