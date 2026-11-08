ARG PHP_VERSION=8.5
ARG NODE_VERSION=26

FROM node:${NODE_VERSION} AS node-base

FROM serversideup/php:${PHP_VERSION}-frankenphp AS base
USER root
RUN install-php-extensions intl gd bcmath
ENV PHP_DATE_TIMEZONE=UTC \
    PHP_MEMORY_LIMIT=256M \
    PHP_MAX_EXECUTION_TIME=90 \
    PHP_UPLOAD_MAX_FILE_SIZE=100M \
    PHP_POST_MAX_SIZE=100M
WORKDIR /var/www/html

FROM base AS dev
ARG USER_ID=1000
ARG GROUP_ID=1000
COPY --from=node-base /usr/local/bin/ /usr/local/bin/
COPY --from=node-base /usr/local/lib/node_modules/ /usr/local/lib/node_modules/
RUN install-php-extensions xdebug pcov \
    && apt-get update \
    && apt-get install -y --no-install-recommends git postgresql-client \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-serversideup-set-id www-data ${USER_ID}:${GROUP_ID} \
    && docker-php-serversideup-set-file-permissions --owner ${USER_ID}:${GROUP_ID}
USER www-data

FROM base AS vendor
COPY --chown=www-data:www-data . /var/www/html
USER www-data
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM node-base AS node-build
WORKDIR /app
COPY . /app
COPY --from=vendor /var/www/html/vendor /app/vendor
RUN npm ci --no-audit && npm run build

FROM base AS prod
ENV AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_OPTIMIZE=true \
    AUTORUN_LARAVEL_MIGRATION=false \
    PHP_OPCACHE_ENABLE=1 \
    SSL_MODE=off
COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor
COPY --from=vendor --chown=www-data:www-data /var/www/html/bootstrap/cache /var/www/html/bootstrap/cache
COPY --from=node-build --chown=www-data:www-data /app/public/build /var/www/html/public/build
USER www-data
