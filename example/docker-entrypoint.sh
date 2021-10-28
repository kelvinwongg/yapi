#!/bin/sh
pecl install xdebug && docker-php-ext-enable xdebug
/usr/local/bin/docker-php-entrypoint apache2-foreground