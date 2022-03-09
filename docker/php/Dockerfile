FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    imagemagick \
    libmagickwand-dev

RUN docker-php-ext-install zip

RUN docker-php-ext-install pdo_mysql mysqli

RUN docker-php-ext-install gd

RUN pecl install imagick
RUN docker-php-ext-enable imagick

RUN pecl install apcu-5.1.18
RUN docker-php-ext-enable apcu

RUN docker-php-ext-install xml

RUN pecl install xdebug-2.9.6
RUN docker-php-ext-enable xdebug

RUN echo 'xdebug.remote_port=9001' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo 'xdebug.remote_autostart=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo 'xdebug.remote_connect_back=0' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_host = $(getent hosts docker.for.mac.localhost | awk '{ print $1 }')" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

RUN apt-get install git unzip wait-for-it wget libicu-dev -y && \
    docker-php-ext-install intl pdo_mysql && \
    echo 'extension=intl.so' > /usr/local/etc/php/conf.d/docker-php-ext-intl.ini

WORKDIR /usr/src/app

#COPY apps/symfony-app /usr/src/app

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

RUN \
	echo 'PS1="\[\\e[0;94m\]${debian_chroot:+($debian_chroot)}\\u@\\h:\\w\\\\$\[\\e[m\] "' >> ~/.bashrc

RUN \
  curl -sL https://deb.nodesource.com/setup_12.x | bash - \
  && apt-get install -y nodejs

RUN npm install -g yarn