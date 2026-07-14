FROM php:8.2-apache

# Habilitar mod_rewrite para .htaccess
RUN a2enmod rewrite

# Instalar extensiones PHP comunes
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar Apache para escuchar en puerto 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

# Configurar Apache para permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Suprimir warning de ServerName
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar archivos de la app
COPY agenciaunica/ /var/www/html/

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

CMD ["apache2-foreground"]
