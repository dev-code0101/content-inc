# -------------------------------------------------
# 1️⃣ Laravel (PHP 8.3) + Composer
# -------------------------------------------------
FROM php:8.3-fpm-alpine AS base

# System deps -------------------------------------------------
RUN apk add --no-cache \
	git \
	curl \
	libpng-dev \
	libjpeg-turbo-dev \
	libwebp-dev \
	freetype-dev \
	zip \
	unzip \
	icu-dev \
	oniguruma-dev \
	autoconf \
	g++ \
	make \
	bash

# PHP extensions -------------------------------------------------
RUN docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg \
		--with-webp && \
	docker-php-ext-install -j$(nproc) \
		gd \
		pdo_mysql \
		intl \
		bcmath \
		opcache && \
	docker-php-ext-enable opcache

# Composer -------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first (cache layer)
COPY blog-cms-api/blog-cms-main/composer.json .
COPY blog-cms-api/blog-cms-main/composer.lock .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the rest of the application
COPY blog-cms-api/blog-cms-main .

# Permissions for storage & bootstrap/cache
RUN chown -R www-data:www-data \
		storage \
		bootstrap/cache && \
	chmod -R 775 storage bootstrap/cache

# -------------------------------------------------
# 2️⃣ Production image (tiny)
# -------------------------------------------------
FROM php:8.3-fpm-alpine AS prod
COPY --from=base /usr/local/etc/php /usr/local/etc/php
COPY --from=base /usr/local/lib/php /usr/local/lib/php
COPY --from=base /usr/local/bin /usr/local/bin
COPY --from=base /var/www/html /var/www/html

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
