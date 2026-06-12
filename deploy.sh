#!/usr/bin/env bash
# Staging / production deployment script
# Run on the server: bash deploy.sh

set -e

echo "==> Pulling latest code..."
git pull origin main

echo "==> Installing/updating Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Running pending migrations..."
php artisan migrate --force

echo "==> Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "==> Rebuilding optimised caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Ensuring storage symlink exists..."
php artisan storage:link --quiet || true

echo "==> Setting file permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo "Deploy complete."
