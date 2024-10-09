# Use the base image PHP 8.1.7 with Apache
FROM php:8.1.7-apache

# Install necessary packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libzip-dev \
    git \
    unzip \
    cron \
    inotify-tools \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-source delete

# Copy Composer file and install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Apache
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# mod_rewrite
RUN a2enmod rewrite

# Copy the project into the container
COPY . /var/www/html/

# Set file permissions
RUN chown -R www-data:www-data /var/www/html

# Copy cron jobs file
COPY ./cronjobs.txt /etc/cron.d/cronjobs

RUN chmod +x /var/www/html/bin/console

# Give proper permissions to cron jobs file and set it up
RUN chmod 0644 /etc/cron.d/cronjobs

# Apply cron job
RUN crontab /etc/cron.d/cronjobs

# Create a script to monitor cronjobs.txt
COPY ./monitor_cron.sh /usr/local/bin/monitor_cron.sh
RUN chmod +x /usr/local/bin/monitor_cron.sh

# Start cron and Apache
CMD cron && /usr/local/bin/monitor_cron.sh && apache2-foreground

# Expose port 80 for web server access
EXPOSE 80
