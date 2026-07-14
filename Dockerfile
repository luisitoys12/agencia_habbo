FROM php:8.2-apache

# Instalar MySQL, supervisord y extensiones PHP
RUN apt-get update && apt-get install -y \
    default-mysql-server \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Puerto 8080 para Hyperlift
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess y suprimir warning
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar app
COPY agenciaunica/ /var/www/html/

# Copiar config de supervisor y script de inicio
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY init-db.sh /init-db.sh
RUN chmod +x /init-db.sh

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

CMD ["/init-db.sh"]
