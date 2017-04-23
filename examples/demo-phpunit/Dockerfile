FROM php:7.0.7-cli

RUN pecl install xdebug-2.4.0 \
    && docker-php-ext-enable xdebug

# ADD ./php.ini /usr/local/etc/php/conf.d

WORKDIR /var/www/my_app

CMD ["vendor/bin/phpunit", "--coverage-text"]