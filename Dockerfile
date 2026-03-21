FROM php:8.3-cli

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    # Required by pdo_pgsql extension build (provides libpq-fe.h)
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    ca-certificates \
    gnupg \
 && docker-php-ext-install intl zip pdo pdo_pgsql \
 && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install Node.js 22 (for Vite build)
RUN mkdir -p /etc/apt/keyrings \
 && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
    | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
 && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" \
    > /etc/apt/sources.list.d/nodesource.list \
 && apt-get update \
 && apt-get install -y --no-install-recommends nodejs \
 && rm -rf /var/lib/apt/lists/*

COPY . .

RUN composer install --no-dev --optimize-autoloader \
 && npm ci \
 && npm run build

EXPOSE 8000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
