FROM php:8.3-apache

RUN apt-get update \
  && apt-get install --no-install-recommends -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    graphviz \
    libcurl4-openssl-dev \
    pkg-config

RUN apt-get install --no-install-recommends libpq-dev -y

RUN docker-php-ext-install pgsql 
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install intl
RUN docker-php-ext-install zip
RUN docker-php-ext-install exif
RUN docker-php-ext-install opcache
RUN docker-php-ext-install soap
#
## mcrypt install
# https://pecl.php.net/package/mcrypt
RUN pecl install mcrypt-1.0.7
RUN docker-php-ext-enable mcrypt
#
RUN docker-php-source delete
RUN apt-get install -y libfcgi0ldbl
RUN apt-get install -y nano
RUN apt-get install -y iputils-ping

# RUN apt-get install --no-install-recommends nginx=1.18.* -y
RUN apt-get remove libicu-dev icu-devtools -y

WORKDIR /var/www
## COPY ./nginxConf/ /etc/nginx/conf.d/
# COPY ./deploy/nginxConf /etc/nginx/conf.d/
RUN a2enmod rewrite
RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini
ADD ./deploy/apache.conf /etc/apache2/sites-available/000-default.conf
RUN service apache2 restart
# apache2 -t
# COPY ./nginxConf/static-site /etc/nginx/sites-enabled/static-site
# RUN ln -s /etc/nginx/sites-available/static-site /etc/nginx/sites-enabled/
# COPY ./nginxConf/static-site /etc/nginx/sites-available/static-site

COPY . /var/www/html
RUN chmod 777 -R /var/www/html/writable/cache/

# COPY ./deploy/entrypoint.sh /etc/entrypoint.sh
# RUN chmod +x /etc/entrypoint.sh

# SETUP PHP-FPM CONFIG SETTINGS (max_children / max_requests)
# RUN echo 'pm.max_children = 900' >> /usr/local/etc/php-fpm.d/zz-docker.conf
# RUN echo 'pm.start_servers = 95' >> /usr/local/etc/php-fpm.d/zz-docker.conf
# RUN echo 'pm.min_spare_servers = 50' >> /usr/local/etc/php-fpm.d/zz-docker.conf
# RUN echo 'pm.max_spare_servers = 150' >> /usr/local/etc/php-fpm.d/zz-docker.conf
# RUN echo 'pm.max_requests = 600' >> /usr/local/etc/php-fpm.d/zz-docker.conf

EXPOSE 80
# EXPOSE 9000

# ENTRYPOINT ["/etc/entrypoint.sh"]

# https://stackoverflow.com/questions/46332919/combining-php-fpm-with-nginx-in-one-dockerfile
# # Run
#!/usr/bin/env bash
# service nginx start
# php-fpm

## Run v2
#!/usr/bin/env sh
# set -e
# php-fpm -D
# nginx -g 'daemon off;'