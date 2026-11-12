#!/bin/sh
set -eu

cd /app

mkdir -p \
  storage/app/public \
  storage/app/private \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
  if [ -f storage/app/.docker-app-key ]; then
    APP_KEY=$(tr -d '\r\n' < storage/app/.docker-app-key)
    echo "Using APP_KEY persisted in the storage volume."
  else
    APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    printf '%s\n' "$APP_KEY" > storage/app/.docker-app-key
    chmod 600 storage/app/.docker-app-key
    echo "APP_KEY was empty. Generated one and stored it in the storage volume."
  fi
  export APP_KEY
fi

echo "Waiting for PostgreSQL at ${DB_HOST:-db}:${DB_PORT:-5432}..."
i=0
while [ "$i" -lt 60 ]; do
  if php /usr/local/bin/wait-for-db.php; then
    break
  fi
  i=$((i + 1))
  sleep 2
done

if [ "$i" -ge 60 ]; then
  echo "PostgreSQL did not become ready in time." >&2
  exit 1
fi

php artisan migrate --force --no-interaction
php artisan storage:link --no-interaction || true
php artisan config:cache --no-interaction
php artisan view:cache --no-interaction || echo "view:cache skipped"

exec frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile
