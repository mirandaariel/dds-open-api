
# Use the official PHP image.
# https://hub.docker.com/_/php
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    procps

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure PHP for Cloud Run.
# Precompile PHP code with opcache.
RUN docker-php-ext-install -j "$(nproc)" opcache
RUN set -ex; \
  { \
    echo "; Cloud Run enforces memory & timeouts"; \
    echo "memory_limit = -1"; \
    echo "max_execution_time = 0"; \
    echo "; File upload at Cloud Run network limit"; \
    echo "upload_max_filesize = 32M"; \
    echo "post_max_size = 32M"; \
    echo "; Configure Opcache for Containers"; \
    echo "opcache.enable = On"; \
    echo "opcache.validate_timestamps = Off"; \
    echo "; Configure Opcache Memory (Application-specific)"; \
    echo "opcache.memory_consumption = 32"; \
    echo "; Disable X-Powered-By header"; \
    echo "expose_php = Off"; \
  } > "$PHP_INI_DIR/conf.d/cloud-run.ini"

# Enable Apache modules
RUN a2enmod rewrite headers ssl
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Copy in custom code from the host machine.
WORKDIR /var/www/html
COPY . ./

# Install Google Cloud Storage client library
RUN composer require google/cloud-storage

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://github.com/docker-library/docs/blob/master/php/README.md#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Enable .htaccess files
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Add CSP header configuration -
RUN echo "\n\
<IfModule mod_headers.c>\n\
    Header set Content-Security-Policy \"default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'; upgrade-insecure-requests\"\n\
    Header unset X-Powered-By\n\
    Header always set Strict-Transport-Security \"max-age=300; includeSubDomains; preload\"\n\
    Header always set X-Frame-Options \"SAMEORIGIN\"\n\
    Header always set X-Content-Type-Options \"nosniff\"\n\
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"\n\
    Header always set Permissions-Policy \"geolocation=(), microphone=(), camera=()\"\n\
</IfModule>" >> /etc/apache2/conf-available/security.conf

RUN a2enconf security

# Create a startup script
RUN echo '#!/bin/bash\n\
# Attempt to disable TCP timestamps\n\
if [ -w /proc/sys/net/ipv4/tcp_timestamps ]; then\n\
    echo 0 > /proc/sys/net/ipv4/tcp_timestamps\n\
fi\n\
\n\
# Start Apache in foreground\n\
apache2-foreground' > /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

# Use the startup script as the Docker entrypoint
ENTRYPOINT ["/usr/local/bin/startup.sh"]