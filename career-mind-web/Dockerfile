# Career Mind — web app (PHP + Apache)
FROM php:8.2-apache

# PDO MySQL driver (the app connects via PDO mysql).
RUN docker-php-ext-install pdo_mysql

# The app's front controller and .htaccess live in public/, so point Apache there
# and enable mod_rewrite (the .htaccess routes all requests to index.php).
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
    && a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# Writable dirs for CV uploads and app logs (gitignored, so create them here).
RUN mkdir -p public/uploads/cv storage/logs \
    && chown -R www-data:www-data public/uploads storage

EXPOSE 80
