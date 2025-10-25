#!/bin/bash
# docker-entrypoint.sh

# Démarre PHP-FPM en arrière-plan
php-fpm &

# Démarre Nginx au premier plan
nginx -g "daemon off;"
