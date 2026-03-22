#!/bin/bash
set -e

APP_DIR="/home/workspace/SiAbsen"
PORT="${PORT:-8080}"
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

# Kill any existing php-fpm processes using our port
pkill -f "php-fpm.*$PHP_FPM_PORT" 2>/dev/null || true
sleep 1

# Start PHP-FPM in background
echo "[SiAbsen] Starting PHP-FPM on port $PHP_FPM_PORT..."
php-fpm8.3 -y $PHP_FPM_CONF &
PHP_FPM_PID=$!

# Wait a moment for PHP-FPM to start
sleep 2

# Start nginx in foreground
echo "[SiAbsen] Starting Nginx on port $PORT..."
nginx -g "daemon off;" &
NGINX_PID=$!

# Function to cleanup on exit
cleanup() {
    echo "[SiAbsen] Shutting down..."
    kill $NGINX_PID 2>/dev/null || true
    kill $PHP_FPM_PID 2>/dev/null || true
    wait
    exit 0
}

trap cleanup SIGTERM SIGINT

# Wait for both processes
wait $PHP_FPM_PID $NGINX_PID
