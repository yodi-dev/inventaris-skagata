#!/bin/sh
set -e

# Port binding (Render defaults to 10000 or $PORT)
export PORT="${PORT:-8080}"
echo "Configuring Nginx on port ${PORT}..."
mkdir -p /etc/nginx/http.d
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

# Set directory permissions for web user
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel configurations
echo "Optimizing Laravel configuration and caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Execute database migrations and initial seeders if database credentials exist
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --seed || true
fi

echo "Starting services via supervisord..."
exec supervisord -c /etc/supervisord.conf
