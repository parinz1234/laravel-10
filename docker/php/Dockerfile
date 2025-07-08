# /docker/php/Dockerfile

# Define build arguments for versioning
ARG PHP_VERSION=8.3
ARG ALPINE_VERSION=3.18

# Use an official PHP image. Alpine is lightweight and great for production.
FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION}

# Set working directory for all subsequent commands
WORKDIR /var/www/html

# Install permanent, runtime dependencies (removed memcached)
RUN apk add --no-cache \
    supervisor \
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

# Install temporary build dependencies and PHP extensions (removed memcached)
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
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
    && apk del .build-deps

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create the directory for supervisor logs
RUN mkdir -p /var/log/supervisor

# Copy the Supervisor configuration file into the container
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create a non-root user 'www' to run the application (removed memcache user)
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