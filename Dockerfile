FROM php:8.2-fpm

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances système nécessaires à Laravel et MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source de l'application Laravel
COPY . .

# Installer les dépendances Laravel (production)
RUN composer install --optimize-autoloader --no-dev

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port standard FPM
EXPOSE 9000

# Commande de démarrage (PHP-FPM)
CMD ["php-fpm"]
