# Dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpng-dev libjpeg-dev libzip-dev zlib1g-dev libonig-dev curl pkg-config libxml2-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install pdo pdo_mysql intl zip opcache gd

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Create runtime dirs
RUN mkdir -p /app/var /app/public && chown -R www-data:www-data /app

EXPOSE 9000
CMD ["php-fpm"]