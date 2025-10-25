# --- IMAGE DE BASE ---
FROM php:8.2-fpm

# --- DÉFINIR LE RÉPERTOIRE DE TRAVAIL ---
WORKDIR /var/www

# --- INSTALLER DÉPENDANCES SYSTÈME ---
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# --- INSTALLER EXTENSIONS PHP (MySQL) ---
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# --- INSTALLER COMPOSER ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- COPIER LE CODE SOURCE ---
COPY . .

# --- INSTALLER DÉPENDANCES LARAVEL POUR LA PROD ---
RUN composer install --optimize-autoloader --no-dev

# --- OPTIMISATIONS LARAVEL (sans key:generate) ---
RUN php artisan config:cache \
 && php artisan route:cache

# --- DROITS SUR LES DOSSIERS LARAVEL ---
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# --- PORT EXPOSE (PHP-FPM) ---
EXPOSE 9000

# --- COMMANDE DE DÉMARRAGE ---
CMD ["php-fpm"]
