FROM php:7.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

ARG env=production

RUN apt-get update && apt-get install -y \
    git\
    vim \
    sqlite3 \
    wget \
    zip \
    && wget "http://www.adminer.org/latest.php" -O /var/www/html/adminer.php

# INSTALLING COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && export PATH=/root/composer/vendor/bin:$PATH \
    && composer self-update

# INSTALLING Symfony
RUN wget 'https://get.symfony.com/cli/installer' -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Setup Symfony dirs
RUN mkdir -p /var/cache/symfony /var/log/symfony /var/spool/symfony

WORKDIR /var/www/html
