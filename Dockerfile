# Use the official PHP image
FROM php:8.2-apache

# Copy all project files into the container
COPY . /var/www/html

# Install Composer for dependency management (optional, if you need it)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 80 for HTTP
EXPOSE 80

# Configure permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 again (already done earlier, so this is redundant)
EXPOSE 80

# Command to start Apache
CMD ["apache2-foreground"]