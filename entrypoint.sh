#!/bin/sh
set -e

# Clear and cache configurations
php artisan optimize:clear 2>/dev/null || true
php artisan config:cache 2>/dev/null || true

# Run migrations (ignore errors if DB not ready yet)
php artisan migrate --force --no-interaction 2>/dev/null || true

# Start the server using exec to replace shell
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}