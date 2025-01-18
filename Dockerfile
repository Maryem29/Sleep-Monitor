# Use the official PHP image
FROM php:8.2-apache

# Copy all project files into the container
COPY . /var/www/html

# Установка Composer (для управления зависимостями)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 80 for HTTP
EXPOSE 80
# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html

# Открытие порта 80
EXPOSE 80

# Команда для запуска Apache
CMD ["apache2-foreground"]
