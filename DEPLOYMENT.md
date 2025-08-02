# CapRover Deployment Guide

This Laravel application has been dockerized for CapRover deployment.

## Prerequisites

1. A CapRover instance running
2. A MySQL database (can be deployed as a separate app on CapRover)
3. Domain name configured in CapRover

## Environment Variables

Create a `.env` file in your CapRover app with the following variables:

```env
APP_NAME="Brayne Jewelry Manager"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=your-mysql-app.srv.captain.your-domain.com
DB_PORT=3306
DB_DATABASE=jewelry_new
DB_USERNAME=root
DB_PASSWORD=your-database-password

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
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
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

## Deployment Steps

1. **Deploy MySQL Database (if not already deployed):**
   - Create a new app in CapRover
   - Use the `mysql:8.0` image
   - Set environment variables:
     - `MYSQL_ROOT_PASSWORD=your-secure-password`
     - `MYSQL_DATABASE=jewelry_new`
   - Note the internal service name for the DB_HOST

2. **Deploy Laravel Application:**
   - Create a new app in CapRover
   - Connect your Git repository
   - Set the environment variables as shown above
   - Update the `DB_HOST` to point to your MySQL app's internal service name
   - Generate an APP_KEY: `php artisan key:generate --show`

3. **Post-Deployment:**
   - The application will automatically run migrations on startup
   - You may need to seed the database manually if required

## Important Notes

- The Dockerfile automatically builds assets and installs dependencies
- Apache is configured to serve from the `public` directory
- The application waits for the database before starting
- Make sure your MySQL database is accessible from the Laravel app
- Set `APP_DEBUG=false` in production
- Configure proper mail settings for notifications

## Troubleshooting

- Check CapRover logs if the app fails to start
- Ensure database connection is working
- Verify all environment variables are set correctly
- Make sure the domain is properly configured in CapRover 