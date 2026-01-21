FROM php:8.4-fpm-alpine

# Dépendances système
RUN apk add --no-cache \
  bash \
  icu-dev \
  libzip-dev \
  oniguruma-dev \
  postgresql-dev

# Extensions PHP utiles pour Laravel + Postgres
RUN docker-php-ext-install \
  pdo_pgsql \
  mbstring \
  intl \
  zip \
  opcache

WORKDIR /var/www/html
