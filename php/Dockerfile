FROM php:8.4-apache

RUN apt-get update && apt-get install -y libpq-dev


RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client

    RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    zip    

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

