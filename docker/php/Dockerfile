# /docker/php/Dockerfile

# Define build arguments for versioning
ARG PHP_VERSION=8.3
ARG ALPINE_VERSION=3.18

# Use an official PHP image. Alpine is lightweight and great for production.
FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION}

# Set working directory for all subsequent commands
WORKDIR /var/www/html

# FIX: Use a more robust, multi-stage dependency installation.
# 1. Install permanent, runtime dependencies.
RUN apk add --no-cache \
    supervisor \
    memcached \
    libmemcached-libs \
    zlib \
    libzip \
    curl \
    git \
    unzip \
    libxml2 \
    oniguruma \
    openssl \
    freetype \
    libjpeg-turbo \
    libpng \
    libwebp

# 2. Install temporary build dependencies, build the extensions, then remove the build dependencies.
# This ensures a clean build environment and a smaller final image.
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libmemcached-dev \
    zlib-dev \
    libzip-dev \
    libxml2-dev \
    oniguruma-dev \
    openssl-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && docker-php-ext-install \
        bcmath \
        dom \
        fileinfo \
        mbstring \
        pdo \
        pdo_mysql \
        xml \
        zip \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && apk del .build-deps

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create the directory for supervisor logs
RUN mkdir -p /var/log/supervisor

# Copy the Supervisor configuration file into the container
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create a dedicated user for the memcached service to run as.
RUN addgroup -S memcache && adduser -S -G memcache memcache

# Create a non-root user 'www' to run the application
RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www

# Change the ownership of the webroot
RUN chown -R www:www /var/www/html

# Switch to the non-root user for application code
USER www

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Change the final command to start Supervisor, which will manage all our processes
# CMD must be run as root to start system services, so we switch back.
USER root
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]