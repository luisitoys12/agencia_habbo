FROM php:8.2-apache

# Instalar MariaDB, supervisor y dependencias PHP
RUN apt-get update && apt-get install -y \
    default-mysql-server \
    supervisor \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Puerto 8080 para Hyperlift
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess y suprimir warning ServerName
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar código fuente (sistema_agencia como webroot)
COPY sistema_agencia/ /var/www/html/

# Copiar scripts de infraestructura
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY init-db.sh /init-db.sh
RUN chmod +x /init-db.sh

# Crear directorio de logs supervisor
RUN mkdir -p /var/log/supervisor

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

CMD ["/init-db.sh"]
