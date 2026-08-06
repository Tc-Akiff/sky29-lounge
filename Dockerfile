FROM php:8.2-apache

# Pasang peranti SQLite yang diperlukan
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite

# Aktifkan mod rewrite Apache
RUN a2enmod rewrite

# Salin semua fail projek ke dalam pelayan
COPY . /var/www/html/

# Tetapkan kebenaran folder untuk pangkalan data SQLite
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 775 /var/www/html/

# Port pelayan
EXPOSE 80
