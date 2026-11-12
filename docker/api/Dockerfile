FROM dunglas/frankenphp:1-php8.3

WORKDIR /app

RUN install-php-extensions pdo_pgsql gd intl zip bcmath opcache \
 && apt-get update \
 && apt-get install -y --no-install-recommends curl \
 && rm -rf /var/lib/apt/lists/*

COPY docker/api/php.ini /usr/local/etc/php/conf.d/smart-mining.ini
COPY docker/api/Caddyfile /etc/caddy/Caddyfile
COPY docker/api/wait-for-db.php /usr/local/bin/wait-for-db.php
COPY docker/api/entrypoint.sh /usr/local/bin/smart-mining-entrypoint

RUN chmod 755 /usr/local/bin/smart-mining-entrypoint \
 && sed -i 's/\r$//' /usr/local/bin/smart-mining-entrypoint /usr/local/bin/wait-for-db.php

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY api/composer.json api/composer.lock ./
RUN composer install \
      --no-dev \
      --no-interaction \
      --no-scripts \
      --no-autoloader \
      --prefer-dist

COPY api/ ./

RUN composer dump-autoload --optimize --no-dev --no-interaction --no-scripts \
 && mkdir -p \
      storage/app/public \
      storage/app/private \
      storage/framework/cache/data \
      storage/framework/sessions \
      storage/framework/views \
      storage/logs \
      bootstrap/cache \
 && touch /tmp/build.sqlite \
 && export APP_ENV=local \
      APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= \
      DB_CONNECTION=sqlite \
      DB_DATABASE=/tmp/build.sqlite \
      SESSION_DRIVER=array \
      CACHE_STORE=array \
      QUEUE_CONNECTION=sync \
      LOG_CHANNEL=stderr \
 && php artisan package:discover --ansi --no-interaction \
 && php artisan filament:assets --ansi --no-interaction \
 && rm -f bootstrap/cache/config.php

EXPOSE 80

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["/usr/local/bin/smart-mining-entrypoint"]

HEALTHCHECK --interval=10s --timeout=5s --start-period=40s --retries=12 \
  CMD curl -fsS http://127.0.0.1/up || exit 1
