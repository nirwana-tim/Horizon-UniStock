#!/usr/bin/env bash
set -e

echo "=== Starting deployment ==="

cd "$(dirname "$0")"

if [ -d .git ]; then
    git pull --ff-only
fi

composer install --no-dev --prefer-dist --optimize-autoloader

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan optimize

php artisan queue:restart || true

php artisan session:gc || true

echo "=== Deployment complete ==="
