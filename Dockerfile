FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && a2enmod rewrite

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy ALL source code dulu supaya composer bisa load semua file yang diperlukan
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN 

RUN chown -R www-data:www-data storage bootstrap/cache

RUN echo "Listen 8888" > /etc/apache2/ports.conf

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 8888

CMD ["apache2-foreground"]
