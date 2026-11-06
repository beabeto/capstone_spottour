#!/bin/sh
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/uploads

php railway-migrate.php

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}" --no-reload
