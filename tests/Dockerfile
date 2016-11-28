FROM phundament/app:4.3.0-rc1

COPY _build/composer.* /app/

RUN composer install --optimize-autoloader
