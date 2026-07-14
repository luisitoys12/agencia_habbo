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

# Permitir .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar código fuente
COPY sistema_agencia/ /var/www/html/

# Copiar scripts
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY init-db.sh /init-db.sh
COPY mysql-init.sql /mysql-init.sql
RUN chmod +x /init-db.sh

# Inicializar el datadir de MariaDB en BUILD TIME
RUN mysql_install_db --user=mysql --datadir=/var/lib/mysql

# Ejecutar MariaDB con --init-file para crear DB y usuario SIN password root
RUN mysqld_safe --init-file=/mysql-init.sql --user=mysql & \
    sleep 8 && \
    mysqladmin shutdown -u root 2>/dev/null || true

# Crear directorio de logs
RUN mkdir -p /var/log/supervisor

# Permisos apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

CMD ["/init-db.sh"]
