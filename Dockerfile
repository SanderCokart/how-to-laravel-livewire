############################################
# Base Image
############################################
FROM serversideup/php:8.5-fpm-nginx-alpine AS base

USER root
RUN install-php-extensions redis pdo_pgsql
USER www-data

############################################
# Development Image
############################################
FROM base AS development

ARG USER_ID
ARG GROUP_ID

USER root
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID --service nginx

COPY --chown=www-data:www-data . /var/www/html
USER www-data

############################################
# Bun build
############################################
FROM oven/bun:latest as bun-build
WORKDIR /var/www/html

USER root
COPY --chown=www-data:www-data package.json /var/www/html/package.json
RUN bun install
RUN bun run build

############################################
# Production Image
############################################
FROM base AS production

WORKDIR /var/www/html
COPY --chown=www-data:www-data . /var/www/html
COPY --from=bun-build --chown=www-data:www-data /var/www/html/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-ansi

USER www-data
