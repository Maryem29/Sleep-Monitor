# Use the official PHP image with Apache
FROM php:8.2-apache

# Copy all project files into the container
# Copy Firebase service account file to the container
COPY sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json /var/www/html/
# Install Composer for dependency management (if needed)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 80 for HTTP
EXPOSE 80

# Set permissions to ensure Apache can read and write files
# Change ownership of the service account file
RUN chown www-data:www-data /var/www/html/sleep-monitor-3e4c3-firebase-adminsdk-wbxh8-5a53c375bb.json
# Set the working directory to /var/www/html (your root directory)
WORKDIR /var/www/html

# Open port 80 for the application
CMD ["apache2-foreground"]
