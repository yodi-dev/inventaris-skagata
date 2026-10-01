#!/bin/sh
set -e

# Port binding (Render defaults to 10000 or $PORT)
export PORT="${PORT:-8080}"
echo "Configuring Nginx on port ${PORT}..."
mkdir -p /etc/nginx/http.d
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

# Ensure storage and bootstrap directories exist
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Execute database migrations and initial seeders if database credentials exist
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ]; then
    echo "Running database migrations and seeders..."
    php artisan migrate --force --seed || echo "Warning: Migration failed, please check database credentials."
fi

# Optimize Laravel configurations
echo "Optimizing Laravel configuration and caches..."
php artisan config:cache || echo "Warning: config:cache skipped"
php artisan route:cache || echo "Warning: route:cache skipped"
php artisan view:cache || echo "Warning: view:cache skipped"

# Set directory permissions for web user (MUST RUN AFTER ARTISAN COMMANDS)
echo "Fixing permissions for www-data..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Starting services via supervisord..."
exec supervisord -c /etc/supervisord.conf
