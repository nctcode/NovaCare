# ============================================
# NovaCare - Docker Image
# PHP 8.2 + Apache + MySQL extensions
# ============================================
FROM php:8.2-apache

# Cài đặt các extension PHP cần thiết
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Kích hoạt Apache mod_rewrite (cần cho .htaccess)
RUN a2enmod rewrite

# Cấu hình Apache cho phép .htaccess override
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# Tạo thư mục logs nếu chưa có và phân quyền
RUN mkdir -p /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
