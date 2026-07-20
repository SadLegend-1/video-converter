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

# تفعيل موديل Apache للتوجيه
RUN a2enmod rewrite

# نسخ الملفات مباشرة للجذر
COPY . /var/www/html/

# إنشاء مجلدات الرفع والتحويل وإعطاء الصلاحيات
RUN mkdir -p /var/www/html/uploads /var/www/html/converted \
    && chmod -R 777 /var/www/html \
    && chown -R www-data:www-data /var/www/html

# ضبط إعدادات PHP لرفع الفيديوهات
RUN echo "upload_max_filesize = 500M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 505M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 3600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 80
