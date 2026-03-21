#!/bin/sh
set -e

# Default PORT to 8080 if not set
export PORT="${PORT:-8080}"

# Substitute environment variables in the nginx config
envsubst '${PORT}' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp
mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

# Generate .env from OS environment variables so Laravel picks up
# Railway's injected service reference values (e.g. Redis host/port/password)
cd /var/www

php artisan config:clear

# Cache config so all workers share the resolved values
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
