FROM php:8.2-cli

WORKDIR /var/www

# Installer les dépendances
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Copier Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier d'abord seulement composer.json pour le cache Docker
COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copier le reste du code
COPY . .

# Finaliser Composer
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE $PORT

CMD sh -c "php artisan config:cache && \
           php artisan route:cache && \
           php artisan view:cache && \
           php artisan migrate --force && \
           php artisan serve --host=0.0.0.0 --port=$PORT"
