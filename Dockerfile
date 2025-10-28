FROM php:8.2-cli

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances système nécessaires à Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP nécessaires à Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code source Laravel dans le conteneur
COPY . .

# Installer les dépendances Laravel sans les paquets de dev
RUN composer install --optimize-autoloader --no-dev

# Générer une clé d'application (au cas où elle n'existe pas)
RUN php artisan key:generate --force || true

# Donner les bons droits à Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port attendu par Railway
EXPOSE 8080

# -----------------------------
# 🚀 Commandes exécutées au démarrage :
# 1. Nettoyer le cache/config
# 2. Recompiler la config
# 3. Créer le lien storage/public (ignore si déjà présent)
# 4. Supprimer toutes les tables existantes
# 5. Recréer toutes les tables (migrations)
# 6. Lancer le serveur Laravel
# -----------------------------
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan storage:link || true && \
    php artisan db:wipe --force && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8080
