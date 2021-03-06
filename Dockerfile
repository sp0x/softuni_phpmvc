FROM debian:stable-slim
RUN apt-get update && apt-get install -my \
  curl \
  wget \
  php-curl \
  php-fpm \
  php-gd \
  php-xsl \
  php-mysqlnd \  
  php-cli \
  php-intl \
  php-bz2 \
  php-zip \
  php-mbstring \
  git \
  zip \
  php-apcu \
  php-redis \
  php-opcache
RUN mkdir /run/php
#ADD conf/www.conf /etc/php/7.1/fpm/pool.d/www.conf
#ADD conf/php-fpm.conf /etc/php/7.1/fpm/php-fpm.conf
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
RUN ln -snf /usr/share/zoneinfo/Europe/Sofia /etc/localtime
#RUN usermod -a -G 1000 www-data
WORKDIR /var/www/html/symfony
EXPOSE 9000
ENTRYPOINT php-fpm7.3 -F