FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client

# Install PHP extensions required by Symfony
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    intl \
    zip \
    mbstring \
    ctype \
    iconv \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# IMPORTANT — allow composer to run as root in docker
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependencies
ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN php bin/console cache:clear
RUN php bin/console cache:warmup
# Clear cache (ignore failure if DB not ready yet)
RUN php bin/console cache:clear --env=prod || true

# Railway dynamic port
CMD php -S 0.0.0.0:$PORT -t public
