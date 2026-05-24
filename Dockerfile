FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli && \
    (a2dismod mpm_event mpm_worker || true) && \
    a2enmod mpm_prefork && \
    a2enmod rewrite

WORKDIR /var/www/html
COPY . .

EXPOSE 80
CMD ["apache2-foreground"]
