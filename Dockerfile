# Dockerfile corrigé pour PostgreSQL et déploiement stable

FROM php:8.2-fpm

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Installer l'extension PostgreSQL et les autres extensions PHP
# ATTENTION: CHANGEMENT DE pdo_mysql À pdo_pgsql
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Installer Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source de l'application Laravel
COPY . .

# Installer les dépendances Laravel (production)
RUN composer install --optimize-autoloader --no-dev

# REMOVAL: Suppression des commandes de cache dans le Dockerfile
# Ces commandes provoquent l'erreur "Cannot declare class..." car
# elles s'exécutent sans les variables d'environnement finales.
# Les commandes de cache seront gérées par la Startup Command de Railway.
# Supprimez cette section, NE LA PUSHEZ PAS :
# RUN php artisan config:cache && php artisan route:cache

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port standard FPM
EXPOSE 9000

# Commande de démarrage (PHP-FPM)
CMD ["php-fpm"]
