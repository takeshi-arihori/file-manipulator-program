FROM php:8.3-apache-bullseye

RUN pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www
