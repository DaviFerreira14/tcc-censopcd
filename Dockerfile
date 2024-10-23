FROM php:7.4-apache

# Instala a extensão mysqli
RUN docker-php-ext-install mysqli

# Habilita o módulo de rewrite do Apache
RUN a2enmod rewrite
