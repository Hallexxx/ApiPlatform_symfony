# Utilise une image PHP 8.1 avec FPM basée sur Alpine Linux
FROM php:8.1-fpm-alpine

# Installer les dépendances système et les outils nécessaires
RUN apk add --no-cache icu-dev libxml2-dev oniguruma-dev zip unzip git

# Installer les extensions PHP nécessaires
# Si vous utilisez MySQL, utilisez pdo_mysql, pour PostgreSQL remplacez par pdo_pgsql
RUN docker-php-ext-install intl pdo pdo_pgsql opcache

# Installer Composer en copiant l'exécutable depuis l'image officielle de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail de l'application
WORKDIR /var/www

# Copier les fichiers de dépendances pour installer les packages
COPY composer.json composer.lock ./

# Installer les dépendances sans les packages de développement
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copier l'ensemble de l'application dans le conteneur
COPY . .

# Préparer le cache en mode production
RUN php bin/console cache:clear --env=prod
RUN php bin/console cache:warmup --env=prod

# Exposer le port 8000 (Render passe le port via la variable d'environnement $PORT)
EXPOSE 8000

# Commande de démarrage : on utilise le serveur PHP intégré
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
