FROM php:7.1-fpm

WORKDIR /var/www/html
RUN apt-get update && apt-get install -y zlib1g-dev \
        && docker-php-ext-install zip
RUN docker-php-ext-install pdo pdo_mysql
#RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
#RUN docker-php-ext-install gd
#RUN docker-php-ext-enable gd

VOLUME ["/var/www/html"]

#RUN chmod 777 -R /var/www/html/common/runtime
#RUN chmod 777 -R /var/www/html/frontend/runtime
#RUN chmod 777 -R /var/www/html/backend/runtime
#RUN chmod 777 -R /var/www/html/api/runtime
#RUN chmod 777 -R /var/www/html/backend/web/assets
#RUN chmod 777 -R /var/www/html/frontend/web/assets
RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

COPY setup.sh /setup.sh
RUN chmod +x /setup.sh
RUN /setup.sh