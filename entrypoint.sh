#!/bin/sh
set -e

echo "=== SiAbsen Container Starting ==="
echo "PORT: ${PORT:-8000}"
echo "DATABASE_URL: ${DATABASE_URL:-not set}"
echo "RAILWAY_ENVIRONMENT: ${RAILWAY_ENVIRONMENT:-not set}"

# Wait for database to be ready (if DATABASE_URL is set)
if [ -n "$DATABASE_URL" ]; then
    echo "Waiting for database..."
    for i in 1 2 3 4 5; do
        php artisan db:monitor --timeout=1 2>/dev/null && break
        echo "Database not ready, retry $i/5..."
        sleep 2
    done
fi

# Clear and cache configurations
echo "Clearing caches..."
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true

# Run migrations with visible output
echo "Running migrations..."
php artisan migrate --force --no-interaction || echo "Migration warning (continuing anyway)"

# Start the server
echo "Starting Laravel server on 0.0.0.0:${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000} --no-interaction