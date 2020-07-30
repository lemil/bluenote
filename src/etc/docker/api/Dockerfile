FROM php:7.2-apache

RUN apt-get update 
RUN apt-get install -y apt-utils

#RUN apt-get install -y apt-utils
#RUN apt-get install -y zlib1g
#RUN apt-get install -y libxml2
#RUN /usr/local/bin/docker-php-ext-install zip xmlwriter
#RUN apt-get install libssl -y
# Install GD
#RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev
#RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
#RUN docker-php-ext-install gd
# Install XSL
#RUN apt-get install -y libxslt-dev
#RUN docker-php-ext-install xsl
# Install intl
#RUN apt-get install -y libicu-dev
#RUN pecl install intl
#RUN docker-php-ext-install intl
# Install mcrypt
#RUN apt-get install -y libmcrypt-dev
#RUN docker-php-ext-install mcrypt


# Mod Rewrite
RUN a2enmod rewrite

# Mod Headers
RUN a2enmod headers

# Fix Premissions
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data

#install MYSQL and PDO
RUN /usr/local/bin/docker-php-ext-install mysqli pdo pdo_mysql 

#install composer
RUN apt-get install -y curl 
RUN apt-get install -y git 
RUN apt-get install -y unzip
RUN docker-php-ext-install sockets
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install sockets
RUN cd ~
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

#RUN composer require php-amqplib/php-amqplib

WORKDIR /var/www
RUN chown www-data:www-data -R html

RUN echo "alias ll='ls -la --color'" >> /root/.bashrc

ADD envars.conf /etc/apache2/conf-enabled/envars.conf




