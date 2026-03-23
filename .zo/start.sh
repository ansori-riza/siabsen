#!/bin/bash
set -e

APP_DIR="/home/workspace/SiAbsen"
PORT="${PORT:-8000}"
PHP_FPM_PORT=9001

echo "[SiAbsen] Starting Laravel application on port $PORT..."

# Ensure storage directories exist
mkdir -p $APP_DIR/storage/logs
mkdir -p $APP_DIR/storage/framework/cache
mkdir -p $APP_DIR/storage/framework/sessions
mkdir -p $APP_DIR/storage/framework/views
mkdir -p $APP_DIR/bootstrap/cache
chmod -R 755 $APP_DIR/storage $APP_DIR/bootstrap/cache

# Create nginx config with dynamic port
mkdir -p $APP_DIR/.zo
cat > $APP_DIR/.zo/nginx-runtime.conf << EOF
server {
    listen $PORT;
    listen [::]:$PORT;
    server_name localhost;
    root $APP_DIR/public;
    index index.php;

    client_max_body_size 50M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php$ {
        fastcgi_pass 127.0.0.1:$PHP_FPM_PORT;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }

    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Create php-fpm config
mkdir -p /run/php
PHP_FPM_CONF="$APP_DIR/.zo/php-fpm.conf"

cat > $PHP_FPM_CONF << FPMEOF
[global]
pid = /run/php/php8.3-fpm.pid
error_log = /home/workspace/SiAbsen/storage/logs/php-fpm.log
daemonize = no

[www]
listen = 127.0.0.1:$PHP_FPM_PORT
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 20
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
pm.max_requests = 500
user = www-data
group = www-data
chdir = /home/workspace/SiAbsen
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M
php_admin_value[memory_limit] = 256M
FPMEOF

# Create a minimal nginx.conf that includes our server block
mkdir -p /etc/nginx/conf.d
cat > /etc/nginx/nginx.conf << 'NGINXEOF'
user www-data;
worker_processes auto;
pid /run/nginx.pid;
error_log /var/log/nginx/error.log;

events {
    worker_connections 768;
}

http {
    sendfile on;
    tcp_nopush on;
    types_hash_max_size 2048;
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    access_log /var/log/nginx/access.log;
    gzip on;

    include /etc/nginx/conf.d/*.conf;
}
NGINXEOF

# Copy our server config
cp $APP_DIR/.zo/nginx-runtime.conf /etc/nginx/conf.d/siabsen.conf

# Remove any default configs that might conflict
rm -f /etc/nginx/conf.d/default.conf /etc/nginx/sites-enabled/default 2>/dev/null || true

# Test nginx config
nginx -t || exit 1

# Check if PHP-FPM is already running on our port
PHP_FPM_RUNNING=0
if lsof -i :$PHP_FPM_PORT >/dev/null 2>&1; then
    echo "[SiAbsen] PHP-FPM already running on port $PHP_FPM_PORT"
    PHP_FPM_RUNNING=1
    # Get the main process PID
    PHP_FPM_PID=$(lsof -t -i :$PHP_FPM_PORT | head -1)
fi

# Start PHP-FPM if not running
if [ $PHP_FPM_RUNNING -eq 0 ]; then
    echo "[SiAbsen] Starting PHP-FPM on port $PHP_FPM_PORT..."
    php-fpm8.3 -y $PHP_FPM_CONF &
    PHP_FPM_PID=$!
    
    # Wait for PHP-FPM to be ready
    for i in {1..15}; do
        if nc -z 127.0.0.1 $PHP_FPM_PORT 2>/dev/null; then
            echo "[SiAbsen] PHP-FPM is ready on port $PHP_FPM_PORT"
            break
        fi
        sleep 1
    done
    
    if ! nc -z 127.0.0.1 $PHP_FPM_PORT 2>/dev/null; then
        echo "[SiAbsen] ERROR: PHP-FPM is not listening on port $PHP_FPM_PORT!"
        exit 1
    fi
fi

# Check if Nginx is already running on our port
NGINX_RUNNING=0
if lsof -i :$PORT >/dev/null 2>&1; then
    echo "[SiAbsen] Nginx already running on port $PORT"
    NGINX_RUNNING=1
    NGINX_PID=$(lsof -t -i :$PORT | head -1)
fi

# Start Nginx if not running
if [ $NGINX_RUNNING -eq 0 ]; then
    echo "[SiAbsen] Starting Nginx on port $PORT..."
    nginx -g "daemon off;" &
    NGINX_PID=$!
fi

# Function to cleanup on exit
cleanup() {
    echo "[SiAbsen] Shutting down..."
    if [ $NGINX_RUNNING -eq 0 ]; then
        kill $NGINX_PID 2>/dev/null || true
    fi
    if [ $PHP_FPM_RUNNING -eq 0 ]; then
        kill $PHP_FPM_PID 2>/dev/null || true
    fi
    exit 0
}

trap cleanup SIGTERM SIGINT

# Keep the script running and monitor processes
echo "[SiAbsen] Application started successfully"
while true; do
    sleep 30
    # Just keep running - supervisor will handle restarts
done