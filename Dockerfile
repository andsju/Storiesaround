FROM php:8.3-apache
# FROM php:7.4-apache

RUN apt-get update && apt-get upgrade -y

RUN service apache2 restart
RUN apt-get update && apt-get install -y \
    openssl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    libzip-dev
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install calendar gd mysqli pdo pdo_mysql zip

RUN a2enmod ssl rewrite

# Patch the global SSL config to prevent passphrase error
RUN sed -i 's|^SSLPassPhraseDialog.*|SSLPassPhraseDialog builtin|' /etc/apache2/mods-available/ssl.conf

# Create directory for SSL certificate
RUN mkdir /etc/apache2/ssl

# Generate self-signed certificate
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/apache2/ssl/apache.key \
  -out /etc/apache2/ssl/apache.crt \
  -subj "/C=US/ST=Local/L=Local/O=Dev/OU=Dev/CN=localhost"

# Enable SSL virtual host
RUN a2ensite default-ssl

EXPOSE 443
