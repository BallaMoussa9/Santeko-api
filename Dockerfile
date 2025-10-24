FROM php:8.2-fpm

# Définir le répertoire de travail
WORKDIR /var/www

# Installer dépendances système : AJOUT de libpq-dev pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP : AJOUT de pdo_pgsql pour Railway
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source
COPY . .

# Installer les dépendances Laravel pour la production
RUN composer install --optimize-autoloader --no-dev

# Optimisations Laravel pour la production
RUN php artisan key:generate --force
RUN php artisan config:cache
RUN php artisan route:cache

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Nettoyage : Suppression des dépendances de démarrage locales (wait-for-mysql.sh n'est plus nécessaire)
# Votre script wait-for-mysql.sh et la dépendance netcat ne sont pas utiles ici.

# Port exposé (standard FPM)
EXPOSE 9000
# Note : Railway utilise son propre proxy et expose le port 80/443. Le port 9000 est pour FPM.

# Commande de démarrage : Démarrer PHP-FPM (la commande de migration sera dans les Settings de Railway)
CMD ["php-fpm"]
