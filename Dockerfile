FROM php:8.2-cli

# Répertoire de travail
WORKDIR /var/www

# Dépendances système
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libmariadb-dev libmariadb-dev-compat \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier seulement les fichiers Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copier le reste du code
COPY . .

# Finaliser Composer et nettoyer
RUN composer dump-autoload --optimize

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Commande de démarrage
# Render fournit le port via $PORT.
CMD sh -c "php artisan config:cache && \
           php artisan route:cache && \
           php artisan view:cache && \
           php artisan migrate --force && \
           php artisan serve --host=0.0.0.0 --port=$PORT"

# NOTE : Pour que Render détecte le port, vous devez vous assurer que $PORT est bien défini dans l'environnement de Render.
# EXPOSE n'est pas nécessaire ici, car le port est défini dynamiquement par Render.
