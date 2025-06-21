# PHP Apache image
FROM php:8.1-apache

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Fayllarni web rootga ko‘chirish
COPY . /var/www/html/

# Apache portini ochish
EXPOSE 80
