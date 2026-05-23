FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev

# Install PHP extensions required by Laravel
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Enable Apache rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ✅ Increase memory (IMPORTANT FIX)
ENV COMPOSER_MEMORY_LIMIT=-1

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Set Laravel public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD php artisan migrate --force && apache2-foreground
