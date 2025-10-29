FROM php:8.2-cli

WORKDIR /var/www

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    libmysqlclient-dev \
    && rm -rf /var/lib/apt/lists/*

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier uniquement composer.json et composer.lock pour le cache Docker
COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Copier le reste du code
COPY . .

# Finaliser Composer
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port (défini via Docker Compose ou .env)
ARG PORT=8000
EXPOSE ${PORT}

# Entrypoint simplifié : on peut exécuter les migrations et lancer le serveur
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT}"]
