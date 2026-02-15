FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
RUN php bin/console cache:clear --env=prod
RUN php bin/console cache:warmup --env=prod

CMD php -S 0.0.0.0:8080 -t public
