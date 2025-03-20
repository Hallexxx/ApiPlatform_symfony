# Utilise une image PHP 8.2 avec FPM basée sur Alpine Linux
FROM php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    zip \
    unzip \
    git \
    postgresql-dev

RUN docker-php-ext-install intl pdo pdo_pgsql opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copier les fichiers indispensables pour l'installation
COPY composer.json composer.lock .env ./
COPY bin/ bin/
COPY config/ config/
COPY src/ src/

# Installer les dépendances sans exécuter les scripts post-install
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copier le reste du projet (public, migrations, etc.)
COPY . .

# Exécuter manuellement les scripts nécessaires
RUN php bin/console cache:clear --env=prod
RUN php bin/console cache:warmup --env=prod

EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
