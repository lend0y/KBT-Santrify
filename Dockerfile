# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Install ekstensi PHP yang umum digunakan (misalnya mysqli)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy semua file ke dalam direktori root Apache
COPY . /var/www/html/

# Beri izin yang tepat
RUN chown -R www-data:www-data /var/www/html

# Aktifkan modul Apache rewrite (jika perlu)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html
