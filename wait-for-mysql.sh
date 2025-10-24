#!/bin/sh
# wait-for-mysql.sh
# Attendre que MySQL soit prêt avant de lancer Laravel

echo "Waiting for MySQL at $MYSQLHOST:$MYSQLPORT..."

until nc -z -v -w30 $MYSQLHOST $MYSQLPORT
do
  echo "Waiting for database..."
  sleep 3
done

echo "MySQL is up! Running migrations..."

# Exécuter les migrations Laravel
php artisan migrate --force

# Démarrer le serveur PHP
php artisan serve --host=0.0.0.0 --port=8080
