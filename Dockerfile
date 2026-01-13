FROM php:8.4.11-bullseye
RUN mkdir -p /app
COPY . /app
WORKDIR /app

# Install PHP MariaDB extension and ZIP support
RUN apt-get update && \
    apt-get install -y default-libmysqlclient-dev \
    libzip-dev \
    unzip \
    zip && \
    docker-php-ext-install mysqli pdo_mysql zip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

#install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

RUN composer install

ENTRYPOINT ["php", "artisan", "serve","--host","0.0.0.0","--port","8000"]