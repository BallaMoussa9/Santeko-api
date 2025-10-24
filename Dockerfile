FROM php:8.2-fpm

# Définir le répertoire de travail
WORKDIR /var/www

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source
COPY . .

# Installer les dépendances Laravel
RUN composer install --optimize-autoloader --no-dev

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Port exposé
EXPOSE 9000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=9000
