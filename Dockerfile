FROM php:8.2-apache

# PHP eklentileri
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Apache mod_rewrite aktif
RUN a2enmod rewrite && a2enmod headers

# Apache: AllowOverride ve güvenlik başlıkları
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n\
Header always set X-Content-Type-Options "nosniff"\n\
Header always set X-Frame-Options "SAMEORIGIN"\n\
Header always set X-XSS-Protection "1; mode=block"' \
    > /etc/apache2/conf-available/site.conf \
    && a2enconf site

# Kalıcı uploads klasörü (Coolify volume ile mount edilir)
RUN mkdir -p /var/www/html/uploads && chmod 777 /var/www/html/uploads

# Uygulama dosyaları
COPY html/ /var/www/html/

# config.php uploads dışındaki dosyaları oku-yaz yap
RUN find /var/www/html -type f -name "*.php" -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && chmod 777 /var/www/html/uploads

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
