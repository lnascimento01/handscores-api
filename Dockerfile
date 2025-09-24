FROM php:8.3-fpm

# Sistema e libs
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
      ca-certificates \
      git curl unzip zip vim \
      build-essential autoconf pkg-config \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
      libzip-dev libssl-dev locales \
      libicu-dev \ 
      jpegoptim optipng pngquant gifsicle \
    ; \
    rm -rf /var/lib/apt/lists/*

# Extensões PHP
# Removido: --with-xpm (não suportado no PHP 8.3)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" gd pdo_mysql zip exif pcntl sockets intl bcmath opcache

# PECL (redis + xdebug)
RUN pecl install redis \
 && docker-php-ext-enable redis

RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV TZ="America/Sao_Paulo"
WORKDIR /var/www

# Copie primeiro os manifests (cache de camadas melhor)
COPY composer.json composer.lock /var/www/

# Instale sem dev e SEM scripts (evita package:discover no build)
RUN composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader --no-scripts || true

# Agora copie o restante do projeto
COPY . /var/www

# Permissões (como você já tinha)
RUN groupadd -g 1000 www || true \
 && useradd -u 1000 -ms /bin/bash -g www www || true \
 && mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
 && chown -R www:www /var/www \
 && find /var/www -type d -exec chmod 775 {} \; \
 && find /var/www -type f -exec chmod 664 {} \;

# Gere autoload otimizado (sem scripts)
RUN composer dump-autoload -o --no-dev --no-scripts

USER www

EXPOSE 9000
CMD ["php-fpm"]
