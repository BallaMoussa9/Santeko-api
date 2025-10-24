FROM php:8.2-fpm

# Répertoire de travail
WORKDIR /var/www/html

# Dépendances système
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code Laravel
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Générer la clé Laravel
RUN php artisan key:generate --force

# Exposer le port PHP-FPM
EXPOSE 9000

# Lancer le serveur PHP-FPM
CMD ["php-fpm"]
