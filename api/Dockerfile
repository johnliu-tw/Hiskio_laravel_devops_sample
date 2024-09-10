FROM php:8.3-fpm as api

WORKDIR /usr/src

ARG user
ARG uid

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    curl \
    git \
    unzip \
    net-tools \
    supervisor \
    redis-server \
    iputils-ping \
    default-mysql-client


RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user

COPY ./api/composer* /usr/src/
RUN composer install --no-scripts

COPY ./deployment/docker/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY ./deployment/docker/update.sh /usr/src/update.sh

COPY ./api .

RUN php artisan storage:link && \
    chown -R $user:$user /usr/src && \
    chmod -R 775 ./storage ./bootstrap/cache

RUN chmod +x ./update.sh

USER $user

EXPOSE 9000

CMD ["sh", "-c", "php-fpm"]

FROM api AS worker
COPY ./deployment/docker/supervisord.conf /etc/supervisor/conf.d/supervisor.conf
CMD ["sh", "-c", "supervisord -c /etc/supervisor/conf.d/supervisor.conf"]

FROM api AS scheduler
CMD ["sh", "-c", "php /usr/src/artisan schedule:run --verbose --no-interaction"]