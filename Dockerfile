# Use the official PHP image with Apache
FROM php:8.2-apache

# Install dependencies (if needed), e.g., Composer for PHP dependency management
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy the Firebase configuration file
COPY sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json /var/www/html/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json

# Install Composer (for dependency management, if needed)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the web application files into the container
COPY . /var/www/html/
RUN composer install --no-dev --optimize-autoloader

# Set proper permissions for Apache to read the files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for HTTP
EXPOSE 80

# Set the working directory to /var/www/html (your root directory)
WORKDIR /var/www/html

# Run Apache in the foreground
CMD ["apache2-foreground"]

ENV GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json