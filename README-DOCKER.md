# Brayne Jewelry Manager - Dockerized for CapRover

This Laravel application has been dockerized for deployment on CapRover. The application includes a complete jewelry management system with order processing, product management, and user management features.

## 🚀 Quick Deployment

### Prerequisites
- CapRover instance running
- Git repository access
- Domain name configured

### Deployment Steps

1. **Deploy MySQL Database:**
   ```bash
   # In CapRover dashboard, create a new app
   # Use image: mysql:8.0
   # Set environment variables:
   MYSQL_ROOT_PASSWORD=your-secure-password
   MYSQL_DATABASE=jewelry_new
   ```

2. **Deploy Laravel Application:**
   - Create new app in CapRover
   - Connect your Git repository
   - Set environment variables (see DEPLOYMENT.md)
   - Deploy

## 📁 Files Overview

### Docker Configuration
- `Dockerfile` - Main Apache-based Dockerfile
- `Dockerfile.nginx` - Alternative nginx-based Dockerfile
- `nginx.conf` - nginx configuration
- `.dockerignore` - Files to exclude from Docker build
- `captain-definition` - CapRover configuration

### Health & Monitoring
- `health-check.php` - Health check endpoint for CapRover

## 🔧 Configuration

### Environment Variables

Required environment variables for production:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-mysql-app.srv.captain.your-domain.com
DB_PORT=3306
DB_DATABASE=jewelry_new
DB_USERNAME=root
DB_PASSWORD=your-database-password

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
```

### Database Setup

The application uses MySQL with the following features:
- User management (Admin, Factory, Distributor roles)
- Product management with categories, metals, stones, fonts
- Order processing with status tracking
- Customer and distributor management
- Email notifications
- Import/export functionality

## 🏗️ Architecture

### Docker Setup
- **Base Image:** PHP 8.2 with Apache
- **Extensions:** MySQL, GD, ZIP, Intl, and other Laravel requirements
- **Node.js:** For asset compilation (Vite + Tailwind CSS)
- **Composer:** For PHP dependency management

### Application Features
- **Multi-role System:** Admin, Factory, Distributor, Customer
- **Product Management:** Categories, metals, stones, fonts, ring sizes
- **Order Processing:** Status tracking, notifications, templates
- **Import/Export:** CSV handling for data management
- **Real-time Notifications:** Pusher integration
- **Email System:** Dynamic templates and notifications

## 🔍 Health Check

The application includes a health check endpoint at `/health-check.php` that returns:

```json
{
  "status": "healthy",
  "timestamp": "2024-01-01 12:00:00",
  "laravel_version": "12.0.0",
  "environment": "production"
}
```

## 🛠️ Development

### Local Development
```bash
# Clone repository
git clone <repository-url>
cd brayne-jewelry-manager

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
php artisan serve
```

### Docker Development
```bash
# Build and run with docker-compose
docker-compose up -d

# Or build the production image
docker build -t brayne-jewelry .
docker run -p 80:80 brayne-jewelry
```

## 📊 Database Schema

The application includes comprehensive database migrations for:
- Users and authentication
- Products and categories
- Orders and order items
- Customers and distributors
- Notifications and settings
- Email templates

## 🔐 Security Features

- Role-based access control
- CSRF protection
- Input validation and sanitization
- Secure file uploads
- Environment-based configuration

## 📧 Email Features

- Dynamic email templates
- Order status notifications
- General notifications
- SMTP configuration support

## 🚀 Performance

- Asset compilation and caching
- Route and view caching
- Database query optimization
- CDN-ready asset structure

## 📝 Logging

- Application logs in `storage/logs/`
- Error tracking and monitoring
- Database query logging (in debug mode)

## 🔄 Updates

To update the application:
1. Pull latest changes from Git
2. Rebuild Docker image
3. Deploy to CapRover
4. Run migrations: `php artisan migrate --force`

## 🆘 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check DB_HOST points to correct MySQL service
   - Verify database credentials
   - Ensure MySQL is running

2. **Assets Not Loading**
   - Run `npm run build` locally
   - Check Vite configuration
   - Verify public directory permissions

3. **Permission Errors**
   - Ensure storage and bootstrap/cache are writable
   - Check file ownership in Docker container

4. **Email Not Working**
   - Verify SMTP configuration
   - Check mail server credentials
   - Test with mail logs

### Logs
- Application logs: `storage/logs/laravel.log`
- Docker logs: `docker logs <container-name>`
- CapRover logs: Available in CapRover dashboard

## 📞 Support

For deployment issues:
1. Check CapRover documentation
2. Review application logs
3. Verify environment configuration
4. Test database connectivity

For application issues:
1. Check Laravel logs
2. Verify database migrations
3. Test individual features
4. Review error messages 