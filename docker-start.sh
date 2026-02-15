#!/bin/bash

echo "Waiting for DB..."
sleep 5

echo "Running migrations..."
php bin/console doctrine:database:create --if-not-exists || true
php bin/console doctrine:migrations:migrate --no-interaction || true
php bin/console doctrine:schema:update --force || true

echo "Clearing cache..."
php bin/console cache:clear --env=prod || true

echo "Starting server..."
php -S 0.0.0.0:$PORT -t public
