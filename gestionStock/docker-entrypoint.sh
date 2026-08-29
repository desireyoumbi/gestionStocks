#!/bin/sh
set -e

# Génère la clé d'application si elle n'existe pas encore (APP_KEY vide sur Render)
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Crée le fichier SQLite s'il n'existe pas encore dans ce conteneur
mkdir -p database
touch database/database.sqlite

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Applique les migrations au démarrage du service
php artisan migrate --force

# Render fournit le port à écouter via la variable $PORT
exec php artisan serve --host 0.0.0.0 --port "${PORT:-10000}"