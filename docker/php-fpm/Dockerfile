FROM php:7.4-fpm-alpine

RUN apk update \
    && apk upgrade --available \
    && apk add --virtual build-deps \
        unzip \
        curl \
        wget \
        bash

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /app