FROM php:7.1-rc-fpm

# Fix ipv6 repository stuff
RUN echo "ipv6" >> /etc/modules
RUN apt update

# Install dev stuff
RUN apt install -y \
autoconf \
gcc \
make \
g++ \
zlib1g-dev \
file \
g++ \
libc-dev \
pkgconf \
libmemcached-dev \
libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev \
libssl-dev \
libicu-dev \
musl-dev \
git \
zip \
unzip \
cron

# Install php extensions
RUN docker-php-ext-install pdo_mysql \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install -j4 gd

# Install PECL extensions
RUN pecl install xdebug \
&& docker-php-ext-enable xdebug \
&& pecl install phar \
&& docker-php-ext-install phar \
&& pecl install intl \ 
&& docker-php-ext-install intl

# Configure XDEBUG
RUN echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_autostart=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_host=10.0.2.2" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_log='/var/log/xdebug/xdebug.log'" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Set php-fpm timeout 6 minutes for xdebug
RUN echo "request_terminate_timeout = 180" >> /usr/local/etc/php-fpm.d/www.conf

ADD php.ini /usr/local/etc/php
ADD php-fpm.conf /usr/local/etc/

RUN apt update \
&& apt install -y -u python python-pip \
&& pip install supervisor \
&& mkdir /etc/supervisor

COPY supervisord.conf /etc/supervisor/supervisord.conf


RUN echo "* * * * * www-data /usr/local/bin/php /var/www/app/artisan schedule:run" >> /etc/cron.d/laravel_schedule_job \
&& /usr/bin/crontab /etc/cron.d/laravel_schedule_job \
&& echo "* * * * * /usr/sbin/ntpd -d -q -n -p pool.ntp.org" >> /etc/cron.d/update_time_job \
&& /usr/bin/crontab /etc/cron.d/laravel_schedule_job /etc/crond.d/update_time_job

#RUN /usr/bin/crontab /etc/crontab

WORKDIR /var/www/app

#fucking dumb
RUN mkdir -p /var/run/php
RUN chown www-data:www-data /var/run/php

#RUN chgrp -R www-data /var/www/app/storage /var/www/app/bootstrap/cache \
#&& chmod -R ug+rwx /var/www/app/storage /var/www/app/bootstrap/cache \
#&& chown -R www-data:www-data /var/www/app

#CMD ["supervisord -c /etc/supervisor/supervisord.conf"]

# fix permissions if we're running on OSX
WORKDIR /root
ADD fix-permissions.sh /root/fix-permissions.sh
RUN chmod 777 /root/fix-permissions.sh
RUN ./fix-permissions.sh
WORKDIR /var/www/app

ENTRYPOINT ["/usr/local/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
