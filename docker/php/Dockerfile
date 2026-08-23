# syntax=docker/dockerfile:1

##############################################
# Stage 1: Composer dependencies
##############################################
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/var/cache/apk,sharing=locked \
    apk update && \
    apk add \
        bash \
        curl \
        git \
        icu-data-full \
        tzdata \
        libpng \
        libzip \
        mysql-client \
    && ln -snf /usr/share/zoneinfo/UTC /etc/localtime \
    && echo "UTC" > /etc/timezone

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

##############################################
# Stage 2: Frontend build (Vite / Inertia CSR)
##############################################
FROM node:22-alpine AS frontend
WORKDIR /app
RUN corepack enable && corepack prepare pnpm@latest --activate
COPY package.json pnpm-lock.yaml ./
RUN --mount=type=cache,target=/root/.local/share/pnpm/store,sharing=locked \
    pnpm install --frozen-lockfile
COPY . .
RUN pnpm run build

##############################################
# Stage 2b: Frontend dev image (used by the `vite` service in compose)
# Same pnpm setup as above, but no COPY - code is bind-mounted so the
# container starts instantly and picks up changes live.
##############################################
FROM node:22-alpine AS frontend-dev
RUN corepack enable && corepack prepare pnpm@latest --activate
WORKDIR /var/www/html

##############################################
# Stage 3: Shared PHP base (app / reverb / queue / scheduler all use this)
##############################################
FROM php:8.5-fpm-alpine AS base

RUN --mount=type=cache,target=/var/cache/apk,sharing=locked \
    apk update && \
    apk add \
        bash \
        curl \
        git \
        icu-data-full \
        tzdata \
        libpng \
        libzip \
        mysql-client \
    && ln -snf /usr/share/zoneinfo/UTC /etc/localtime \
    && echo "UTC" > /etc/timezone

# https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        posix \
        bcmath \
        gd \
        intl \
        zip \
        opcache \
        redis \
        sockets

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/99-opcache.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

##############################################
# Stage 4: Development image
# (code is bind-mounted in docker-compose.yml, this stage mainly gives
#  us composer + xdebug for local work)
##############################################
FROM base AS development

ENV APP_ENV=local

COPY docker/php/my.cnf.dev /etc/my.cnf.d/client.cnf

RUN install-php-extensions xdebug
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/99-xdebug.ini
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

##############################################
# Stage 5: Production image
# (self-contained, no bind mounts, code + vendor + built assets baked in)
##############################################
FROM base AS production

ENV APP_ENV=production

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY docker/php/opcache-prod.ini /usr/local/etc/php/conf.d/99-z-opcache-prod.ini

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
