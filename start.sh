#!/bin/sh
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    public/uploads/spots public/uploads/hotels public/uploads/hotel-rooms \
    public/uploads/foods public/uploads/menus public/uploads/profile \
    public/uploads/comments public/uploads/receipts public/uploads/payments \
    public/uploads/gcash-qr

if [ -n "${RAILWAY_VOLUME_MOUNT_PATH:-}" ] && [ "$RAILWAY_VOLUME_MOUNT_PATH" != "/app/public/uploads" ]; then
    echo "WARNING: Upload persistence expects the Railway volume mount path /app/public/uploads; current path is $RAILWAY_VOLUME_MOUNT_PATH" >&2
fi

php railway-migrate.php || true
php railway-ensure-schema.php || true
php railway-seed-admin.php || true

export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}" --no-reload
