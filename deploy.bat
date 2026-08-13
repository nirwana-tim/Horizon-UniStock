@echo off
REM === Deployment script for Windows (local/dev) ===
cd /d "%~dp0"

echo === Starting deployment ===

git pull --ff-only

composer install --prefer-dist --optimize-autoloader

php artisan migrate --force

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

php artisan optimize

php artisan queue:restart

php artisan session:gc

echo === Deployment complete ===
