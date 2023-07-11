FROM php:latest
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
  && apt update \
  && apt install -y zip git libicu-dev locales \
  && docker-php-ext-install intl pdo_mysql opcache \
  && locale-gen fr_FR.UTF-8 \
  && curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin \
  && curl -sS https://get.symfony.com/cli/installer | bash \
  && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
WORKDIR /app
COPY ./composer.json .
RUN composer install
COPY . .
RUN symfony server:ca:install
CMD ["symfony", "serve"]
