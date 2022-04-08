FROM php:8.0.16-fpm-alpine
RUN apk --update add curl git
RUN docker-php-ext-install mysqli pdo pdo_mysql opcache && docker-php-ext-enable pdo_mysql opcache
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
EXPOSE 9000
CMD ["php-fpm", "-F"]
