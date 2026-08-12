# =============================================================================
# Laravel Workshop Management System — Render Deployment
# =============================================================================

# Stage 1: Build dependencies
FROM php:8.3-cli AS builder

# Install system dependencies for PHP extensions and Node.js
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql mbstring bcmath gd xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js 22.x
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy composer files first (better layer caching)
COPY awm/composer.json awm/composer.lock ./

# Install PHP dependencies (no dev for production)
RUN composer install --no-dev --no-interaction --no-scripts --optimize-autoloader

# Copy frontend asset files
COPY awm/package.json awm/package-lock.json ./

# Install npm dependencies
RUN npm ci --production=false

# Copy the rest of the application
COPY awm/ .

# Build frontend assets
RUN npm run build

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Stage 2: Production runtime
FROM php:8.3-cli

# Install runtime system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql mbstring bcmath gd xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy built application from builder stage
COPY --from=builder /var/www/html /var/www/html

# Ensure storage and bootstrap/cache are writable
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force --no-interaction && php artisan serve --host=0.0.0.0 --port=8000"]
