# Use an official PHP image with Apache
FROM php:8.2-apache

# Enable necessary PHP extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy the application files to the container's web directory
COPY ./ /var/www/html/

# Set permissions for the application files
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for the web server
EXPOSE 80