FROM php:8.1-fpm
WORKDIR /app

RUN printf '[PHP]\ndate.timezone = "Europe/Moscow"\n' > /usr/local/etc/php/conf.d/tzone.ini
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Moscow /etc/localtime

RUN apt update && apt install -y unzip
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer pdo_pgsql http
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && apt install -y nodejs && npm install -g yarn

ENV PHP_MEMORY_LIMIT=2G
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY Docker/app/php.ini "$PHP_INI_DIR/php.ini"
COPY Docker/app/ext/* /usr/local/etc/php/conf.d/
COPY composer.json /app/
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY . /app/
RUN chmod g+s /app
RUN chown -R www-data /app
RUN composer install
RUN yarn install && yarn dev && yarn build

CMD ["php-fpm"]
EXPOSE 9000
