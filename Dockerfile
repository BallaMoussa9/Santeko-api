# --- Étape 1 : Construction et Dépendances ---
FROM php:8.2-cli-alpine

# Répertoire de travail
WORKDIR /var/www

# Installer les dépendances système (Version Alpine pour la légèreté)
# Alpine réduit drastiquement la taille de l'image
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    mariadb-connector-c-dev \
    $PHPIZE_DEPS

# Installer les extensions PHP indispensables
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Configurer OPcache pour la production
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de dépendances en premier (Optimisation du cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances sans scripts (ils seront lancés après la copie du code)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copier le reste du code source
COPY . .

# Finaliser l'installation de Composer (Générer l'autoloader optimisé)
RUN composer dump-autoload --optimize --no-dev

# Fixer les permissions pour Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Utiliser l'utilisateur non-root pour plus de sécurité
USER www-data

# Commande de démarrage optimisée pour Render
# On combine les caches et le démarrage. 
# Note: 'php artisan serve' est suffisant pour Render car il gère le proxy inverse.
CMD sh -c "php artisan config:cache && \
           php artisan route:cache && \
           php artisan view:cache && \
           php artisan migrate --force && \
           php artisan serve --host=0.0.0.0 --port=$PORT"



# FROM php:8.2-cli

# # Répertoire de travail
# WORKDIR /var/www

# # Dépendances système
# RUN apt-get update && apt-get install -y \
#     git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
#     libmariadb-dev libmariadb-dev-compat \
#     && rm -rf /var/lib/apt/lists/*

# # Installer les extensions PHP MySQL
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# # Installer Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copier seulement les fichiers Composer
# COPY composer.json composer.lock ./
# RUN composer install --no-dev --optimize-autoloader --no-scripts

# # Copier le reste du code
# COPY . .

# # Finaliser Composer et nettoyer
# RUN composer dump-autoload --optimize

# # Donner les bons droits à Laravel
# RUN chown -R www-data:www-data storage bootstrap/cache

# # Commande de démarrage
# # Render fournit le port via $PORT.
# CMD sh -c "php artisan config:cache && \
#            php artisan route:cache && \
#            php artisan view:cache && \
#            php artisan migrate --force && \
#            php artisan serve --host=0.0.0.0 --port=$PORT"

# # NOTE : Pour que Render détecte le port, vous devez vous assurer que $PORT est bien défini dans l'environnement de Render.
# # EXPOSE n'est pas nécessaire ici, car le port est défini dynamiquement par Render.
