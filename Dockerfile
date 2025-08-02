FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update
RUN apt-get install -y git curl zip unzip supervisor
RUN apt-get install -y libpng-dev libonig-dev libxml2-dev libzip-dev
RUN apt-get install -y libicu-dev libfreetype6-dev libjpeg62-turbo-dev libwebp-dev libsqlite3-dev
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo_sqlite
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN docker-php-ext-install intl

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and build assets
RUN npm install || echo "npm install failed, continuing..."
RUN npm run build || echo "Asset build failed, continuing..." || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/storage/framework/cache \
    && chmod -R 777 /var/www/html/storage/framework/sessions \
    && chmod -R 777 /var/www/html/storage/framework/views

# Configure Apache
RUN a2enmod rewrite
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Create .env file if it doesn'\''t exist\n\
if [ ! -f /var/www/html/.env ]; then\n\
    echo "Creating .env file..."\n\
    cat > /var/www/html/.env << '\''EOF'\''\n\
APP_NAME="Brayne Jewelry Manager"\n\
APP_ENV=production\n\
APP_KEY=\n\
APP_DEBUG=true\n\
APP_URL=http://localhost\n\
\n\
LOG_CHANNEL=stack\n\
LOG_DEPRECATIONS_CHANNEL=null\n\
LOG_LEVEL=debug\n\
\n\
DB_CONNECTION=sqlite\n\
DB_DATABASE=/var/www/html/database/database.sqlite\n\
\n\
BROADCAST_DRIVER=log\n\
CACHE_DRIVER=file\n\
CACHE_STORE=file\n\
SESSION_DRIVER=file\n\
FILESYSTEM_DISK=local\n\
QUEUE_CONNECTION=sync\n\
SESSION_LIFETIME=120\n\
\n\
MEMCACHED_HOST=127.0.0.1\n\
\n\
REDIS_HOST=127.0.0.1\n\
REDIS_PASSWORD=null\n\
REDIS_PORT=6379\n\
\n\
MAIL_MAILER=smtp\n\
MAIL_HOST=mailpit\n\
MAIL_PORT=1025\n\
MAIL_USERNAME=null\n\
MAIL_PASSWORD=null\n\
MAIL_ENCRYPTION=null\n\
MAIL_FROM_ADDRESS="hello@example.com"\n\
MAIL_FROM_NAME="${APP_NAME}"\n\
\n\
AWS_ACCESS_KEY_ID=\n\
AWS_SECRET_ACCESS_KEY=\n\
AWS_DEFAULT_REGION=us-east-1\n\
AWS_BUCKET=\n\
AWS_USE_PATH_STYLE_ENDPOINT=false\n\
\n\
PUSHER_APP_ID=\n\
PUSHER_APP_KEY=\n\
PUSHER_APP_SECRET=\n\
PUSHER_HOST=\n\
PUSHER_PORT=443\n\
PUSHER_SCHEME=https\n\
PUSHER_APP_CLUSTER=mt1\n\
\n\
VITE_APP_NAME="${APP_NAME}"\n\
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"\n\
VITE_PUSHER_HOST="${PUSHER_HOST}"\n\
VITE_PUSHER_PORT="${PUSHER_PORT}"\n\
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"\n\
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"\n\
EOF\n\
fi\n\
\n\
# Ensure storage directories exist and have proper permissions\n\
echo "Setting up storage directories..."\n\
mkdir -p /var/www/html/storage/logs\n\
mkdir -p /var/www/html/storage/framework/cache\n\
mkdir -p /var/www/html/storage/framework/sessions\n\
mkdir -p /var/www/html/storage/framework/views\n\
touch /var/www/html/storage/logs/laravel.log\n\
chmod -R 777 /var/www/html/storage/logs\n\
chmod -R 777 /var/www/html/storage/framework/cache\n\
chmod -R 777 /var/www/html/storage/framework/sessions\n\
chmod -R 777 /var/www/html/storage/framework/views\n\
chown -R www-data:www-data /var/www/html/storage\n\
chmod 666 /var/www/html/storage/logs/laravel.log\n\
chmod -R 777 /var/www/html/bootstrap/cache\n\
\n\
# Ensure cache directory is empty and writable\n\
rm -rf /var/www/html/storage/framework/cache/*\n\
touch /var/www/html/storage/framework/cache/.gitkeep\n\
chmod 777 /var/www/html/storage/framework/cache/.gitkeep\n\
\n\
# Wait for database to be ready (optional)\n\
echo "Checking database connection..."\n\
sleep 5\n\
\n\
# Generate app key if not set\n\
if [ -z "$APP_KEY" ]; then\n\
    echo "Generating application key..."\n\
    php artisan key:generate --force\n\
fi\n\
\n\
# Create SQLite database and run migrations\n\
echo "Setting up SQLite database..."\n\
mkdir -p /var/www/html/database\n\
touch /var/www/html/database/database.sqlite\n\
chmod 666 /var/www/html/database/database.sqlite\n\
\n\
echo "Running database migrations..."\n\
php artisan migrate --force\n\
\n\
# Clear and cache config\n\
echo "Caching configuration..."\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan cache:clear || echo "Cache clear failed, continuing..."\n\
\n\
# Force cache and session to use file system\n\
echo "CACHE_DRIVER=file" >> /var/www/html/.env\n\
echo "SESSION_DRIVER=file" >> /var/www/html/.env\n\
echo "CACHE_STORE=file" >> /var/www/html/.env\n\
\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Start Apache\n\
echo "Starting Apache..."\n\
apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"] 