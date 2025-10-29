FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpq-dev libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader --no-dev
RUN php artisan key:generate --force || true
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8080

CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan storage:link || true && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8080
