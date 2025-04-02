FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    git \
    && docker-php-ext-install zip pdo pdo_mysql

# Konfiguracja PHP
RUN echo "variables_order = \"EGPCS\"" >> /usr/local/etc/php/conf.d/variables_order.ini

# Instalacja Composera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Kopiowanie i ustawienie praw do skryptu startowego
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"] 