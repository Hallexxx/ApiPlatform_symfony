# Utilise une image PHP 8.2 avec FPM basée sur Alpine Linux
FROM php:8.2-fpm-alpine

# Autoriser Composer à s'exécuter en tant que superuser et activer ses plugins
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances système et les outils nécessaires
RUN apk add --no-cache \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    zip \
    unzip \
    git \
    postgresql-dev

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install intl pdo pdo_pgsql opcache

# Installer Composer depuis l'image officielle de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier les fichiers indispensables pour composer (ces fichiers doivent être à la racine du projet)
COPY composer.json composer.lock .env ./
COPY bin/ bin/
COPY config/ config/
COPY src/ src/

# Installer les dépendances sans exécuter les scripts (désactivation des auto-scripts)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copier le reste du projet (public, migrations, templates, etc.)
COPY . .

# Exécuter manuellement les commandes post-install (cache:clear et cache:warmup)
RUN php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod

# Exposer le port 8000
EXPOSE 8000

# Commande de démarrage : utilisation du serveur PHP intégré
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
