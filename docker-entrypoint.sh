#!/bin/sh
set -e

# Ensure SQLite file exists and is writable
if [ ! -f /app/database/database.sqlite ]; then
    touch /app/database/database.sqlite
fi
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database
chmod -R 775 /app/storage /app/bootstrap/cache /app/database

# Run database migrations and seed default portfolio projects
php artisan migrate --force
php artisan db:seed --class=ProjectSeeder --force

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Determine port (Render sets $PORT, default to 80)
PORT="${PORT:-80}"

echo "Starting FrankenPHP on port $PORT..."
exec frankenphp php-server --listen ":${PORT}" --root /app/public

