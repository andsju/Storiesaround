FROM php:8.3-apache
# FROM php:7.4-apache

# RUN apt-get update && apt-get upgrade -y

RUN a2enmod ssl && a2enmod rewrite
RUN mkdir -p /etc/apache2/ssl

RUN service apache2 restart
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    libzip-dev
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install calendar gd mysqli pdo pdo_mysql zip
