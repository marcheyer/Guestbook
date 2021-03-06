FROM ubuntu:16.04

MAINTAINER mheyer


RUN apt-get update \
    && apt-get install -y locales \
    && locale-gen de_DE.UTF-8

ENV LANG de_DE.UTF-8
ENV LANGUAGE de_DE:de
ENV LC_ALL de_DE.UTF-8

RUN apt-get update \
	&& apt-get install -y curl zip unzip git software-properties-common \
	&& add-apt-repository -y ppa:ondrej/php \
	&& apt-get update \
	&& apt-get install -y php7.0-fpm php7.0-cli php7.0-mcrypt php7.0-gd php7.0-mysql \
       php7.0-pgsql php7.0-imap php-memcached php7.0-mbstring php7.0-xml php7.0-curl php7.0-xdebug \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && mkdir /run/php \
    && apt-get remove -y --purge software-properties-common \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ADD php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf
ADD www.conf /etc/php/7.0/fpm/pool.d/www.conf
RUN echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.0/cli/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_host=192.168.99.1" >> /etc/php/7.0/cli/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /etc/php/7.0/cli/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /etc/php/7.0/cli/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_autostart=1" >> /etc/php/7.0/cli/conf.d/20-xdebug.ini

RUN usermod -u 1000 www-data

EXPOSE 9000
CMD ["php-fpm7.0"]
