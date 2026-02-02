# =============================================================================
# ManageX - Dockerfile de Production
# Application Laravel de gestion RH
# Auteur: Akou Melvin
# =============================================================================

# -----------------------------------------------------------------------------
# Stage 1: Build des assets frontend (Vite + Tailwind)
# -----------------------------------------------------------------------------
FROM node:22-alpine AS node-builder

WORKDIR /app

# Copy package files pour le cache des dépendances
COPY package*.json ./

# Installation des dépendances (ci = clean install, plus rapide et reproductible)
RUN npm ci --silent

# Copy des fichiers source pour le build
COPY resources ./resources
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./

# Build des assets de production
RUN npm run build

# -----------------------------------------------------------------------------
# Stage 2: Application PHP Laravel
# -----------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS production

# Labels pour la documentation de l'image
LABEL maintainer="Akou Melvin"
LABEL description="ManageX - Système de gestion RH"
LABEL version="1.0"

# Variables d'environnement pour PHP
ENV PHP_OPCACHE_ENABLE=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES=10000
ENV PHP_OPCACHE_MEMORY_CONSUMPTION=192
ENV PHP_OPCACHE_MAX_WASTED_PERCENTAGE=10

# Installation des dépendances système
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    supervisor \
    nginx \
    shadow \
    # Pour les health checks
    fcgi

# Installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        xml

# Configuration OPcache pour la production
RUN echo "opcache.enable=${PHP_OPCACHE_ENABLE}" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=${PHP_OPCACHE_VALIDATE_TIMESTAMPS}" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=${PHP_OPCACHE_MAX_ACCELERATED_FILES}" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=${PHP_OPCACHE_MEMORY_CONSUMPTION}" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_wasted_percentage=${PHP_OPCACHE_MAX_WASTED_PERCENTAGE}" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Configuration PHP pour la production
RUN echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time=600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_vars=3000" >> /usr/local/etc/php/conf.d/uploads.ini

# Installation de Composer depuis l'image officielle
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copy des fichiers composer pour le cache des dépendances
COPY composer.json composer.lock ./

# Installation des dépendances PHP (sans dev, optimisé)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Copy de tous les fichiers de l'application
COPY . .

# Copy des assets buildés depuis le stage node
COPY --from=node-builder /app/public/build ./public/build

# Génération de l'autoloader optimisé et exécution des scripts post-install
RUN composer dump-autoload --optimize --classmap-authoritative \
    && php artisan package:discover --ansi

# Création des répertoires de stockage Laravel
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    storage/app/public \
    bootstrap/cache

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Création du lien symbolique storage (sera recréé au démarrage si nécessaire)
RUN php artisan storage:link || true

# Copy des configurations Docker
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh

# Permissions du script de démarrage
RUN chmod +x /start.sh

# Création du répertoire pour les logs supervisor
RUN mkdir -p /var/log/supervisor

# Configuration du healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8080}/health || exit 1

# Port exposé (sera remplacé par la variable PORT en runtime)
EXPOSE 8080

# Point d'entrée
CMD ["/start.sh"]
