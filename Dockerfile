FROM php:8.2-apache

# تثبيت FFmpeg والمكتبات الأساسية
RUN apt-get update && apt-get install -y \
    ffmpeg \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt-get/lists/*

# تفعيل موديلات Apache
RUN a2enmod rewrite headers

# ضبط صلاحيات Apache
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

COPY . /var/www/html/

# التأكد من إنشاء وتجهيز كافة مجلدات الرفع المؤقتة
RUN mkdir -p /var/www/html/uploads /var/www/html/converted /tmp \
    && chmod -R 777 /var/www/html /tmp \
    && chown -R www-data:www-data /var/www/html /tmp

# زيادة حدود PHP لأعلى قيمة ممكنة
RUN echo "upload_max_filesize = 500M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 505M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 3600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 1024M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80
