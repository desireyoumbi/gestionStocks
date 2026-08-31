#!/bin/sh
set -e

# Génère la clé d'application si elle n'existe pas encore (APP_KEY vide sur Render)
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Crée le fichier SQLite s'il n'existe pas encore dans ce conteneur
mkdir -p database
touch database/database.sqlite

# Recrée le lien symbolique public/storage -> storage/app/public à chaque démarrage,
# car le système de fichiers du conteneur est éphémère sur Render (comme pour la base SQLite).
# "|| true" évite que le script s'arrête si le lien existe déjà.
php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Applique les migrations au démarrage du service
php artisan migrate --force

# Render fournit le port à écouter via la variable $PORT
exec php artisan serve --host 0.0.0.0 --port "${PORT:-10000}"