#!/bin/sh
cp "/var/www/html/example/php-custom.ini" "$PHP_INI_DIR/conf.d/"
apt-get update && apt-get install -y libyaml-dev \
&& pecl install xdebug && pecl install yaml \
&& docker-php-ext-enable xdebug yaml
/usr/local/bin/docker-php-entrypoint apache2-foreground