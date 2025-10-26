# Utilise PHP 8.2
FROM php:8.2-cli

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances système nécessaires à Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source
COPY . .

# Installer les dépendances Laravel
RUN composer install --optimize-autoloader --no-dev

# Générer la clé d'application (si elle n'existe pas)
RUN php artisan key:generate --force || true

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port que Railway attend
EXPOSE 8080

# Lancer le serveur Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
