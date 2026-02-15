#!/bin/sh

php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

php -S 0.0.0.0:8080 -t public
