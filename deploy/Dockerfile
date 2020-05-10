FROM php:7.1.3-fpm
MAINTAINER < Haryono | onoy - haryonoaryo351@gmail.com >

#====MINIMUM INSTALATION ZONE
RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install -y \
        libpq-dev \
        libmcrypt-dev \
        curl \
    && docker-php-ext-install -j$(nproc) mcrypt \
    && docker-php-ext-install -j$(nproc) pdo \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install  mbstring

RUN apt-get install nano -y
RUN apt-get install supervisor -y
RUN apt-get install -y nginx  && \
    rm -rf /var/lib/apt/lists/*

#====ADD SOURCE ZONE
COPY . /var/www/html
WORKDIR /var/www/html
RUN rm /etc/nginx/sites-enabled/default
COPY ./deploy/deploy.conf /etc/nginx/conf.d/default.conf
RUN mv /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.backup
COPY ./deploy/www.conf /usr/local/etc/php-fpm.d/www.conf

#====SET MOD AND OWN ZONE
RUN usermod -a -G www-data root
RUN chgrp -R www-data storage
RUN chown -R www-data:www-data ./storage
RUN chmod -R 0777 ./storage

#=====CONJOB ZONE
ADD .deploy/crontab /etc/cron.d/hello-cron
RUN chmod 0644 /etc/cron.d/hello-cron
RUN touch /var/log/cron.log
RUN apt-get update -y
RUN apt-get -y install cron
CMD cron && cat /var/log/cron.log

RUN ln -s ./secret/.env .env
RUN chmod +x ./deploy/run

ENTRYPOINT ["./deploy/run"]

EXPOSE 80
