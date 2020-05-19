#!/bin/bash

echo INIT OF BACKEND APPLICATION;

cd /var/www

composer install

php-fpm
