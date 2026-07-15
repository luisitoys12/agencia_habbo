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

# Puerto 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# VirtualHost apuntando a /public
RUN printf '<VirtualHost *:8080>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    DirectoryIndex index.php index.html\n\
    ErrorDocument 404 /index.php\n\
    <Directory /var/www/html/public>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# AllowOverride global
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar codigo fuente
COPY agenciaunica/ /var/www/html/

# Copiar scripts de arranque
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY init-db.sh /init-db.sh
COPY mysql-init.sql /mysql-init.sql
COPY apache-wait-start.sh /usr/local/bin/apache-wait-start.sh
RUN chmod +x /init-db.sh /usr/local/bin/apache-wait-start.sh

# LIMPIEZA TOTAL del datadir para evitar cache corrupta de Kaniko
# Siempre inicializar desde cero en cada build
RUN rm -rf /var/lib/mysql && mkdir -p /var/lib/mysql && chown -R mysql:mysql /var/lib/mysql
RUN mariadb-install-db --user=mysql --datadir=/var/lib/mysql

# Ejecutar MariaDB con init-file para crear DB, usuario y tablas
RUN mysqld_safe --user=mysql & \
    sleep 10 && \
    mysql -u root < /mysql-init.sql && \
    mysqladmin shutdown -u root 2>/dev/null || true && \
    sleep 3

# Directorios de logs
RUN mkdir -p /var/log/supervisor

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

CMD ["/init-db.sh"]
