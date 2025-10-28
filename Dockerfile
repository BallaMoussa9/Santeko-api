FROM php:8.2-apache

# Dossier de travail
WORKDIR /var/www/html

# Installer les dépendances nécessaires à Laravel et PostgreSQL
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev curl libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Copier Composer depuis l’image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers Laravel
COPY . .

# Installer les dépendances Laravel
RUN composer install --optimize-autoloader --no-dev

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Supprimer les caches
RUN php artisan config:clear && php artisan cache:clear

# Exposer le port utilisé par Render
EXPOSE 10000

# Commande de démarrage
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
