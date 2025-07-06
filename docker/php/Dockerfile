FROM php:8.3-fpm

COPY php.ini /usr/local/etc/php/

# MySQLとZIPのサポートをインストール
RUN apt update \
	&& apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
	&& docker-php-ext-install pdo_mysql zip

# composerをダウンロードしてインストール
RUN curl -sS https://getcomposer.org/installer | php \
	&& mv composer.phar /usr/local/bin/composer \
	&& composer self-update

# 画像処理に必要な開発ライブラリをインストール
RUN apt-get update && apt-get install -y \
	libpng-dev \
	libjpeg-dev \
	libfreetype6-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install gd

WORKDIR /var/www