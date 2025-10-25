FROM php:8.2-fpm

# Installer les dépendances système, y compris Nginx et les outils nécessaires
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source de l'application Laravel
COPY . .

# Installer les dépendances Laravel (production)
RUN composer install --optimize-autoloader --no-dev

# ----------------- NGINX CONFIGURATION -----------------
# Supprimer la configuration Nginx par défaut
RUN rm /etc/nginx/sites-enabled/default
# Copier votre configuration nginx personnalisée
COPY nginx.conf /etc/nginx/sites-available/default
# Lier le fichier de configuration pour l'activer
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
# --------------------------------------------------------

# Exposer le port du serveur web (Nginx)
EXPOSE 8080

# Définir le point d'entrée pour démarrer PHP-FPM et Nginx ensemble
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]





# FROM php:8.2-fpm

# # Définir le répertoire de travail
# WORKDIR /var/www

# # Installer les dépendances système nécessaires à Laravel et MySQL
# RUN apt-get update && apt-get install -y \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     git \
#     curl \
#     && rm -rf /var/lib/apt/lists/*

# # Installer les extensions PHP nécessaires
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# # Installer Composer depuis l'image officielle
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copier le code source de l'application Laravel
# COPY . .

# # Installer les dépendances Laravel (production)
# RUN composer install --optimize-autoloader --no-dev

# # Donner les bons droits à Laravel
# RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# # Exposer le port standard FPM
# EXPOSE 9000

# # Commande de démarrage (PHP-FPM)
# CMD ["php-fpm"]
