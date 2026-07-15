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

# Puerto 8080 para Fly.io / kusmedios.lat
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# Escribir VirtualHost completo apuntando a /public
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
    # Alias para que /landing sirva la landing publica\n\
    Alias /landing /var/www/html/landing.php\n\
    <Files /var/www/html/landing.php>\n\
        Require all granted\n\
    </Files>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# AllowOverride global y ServerName
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Copiar codigo fuente
COPY agenciaunica/ /var/www/html/

# Copiar scripts
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY init-db.sh /init-db.sh
COPY mysql-init.sql /mysql-init.sql
RUN chmod +x /init-db.sh

# MariaDB datadir init en BUILD TIME
RUN mysql_install_db --user=mysql --datadir=/var/lib/mysql

RUN mysqld_safe --init-file=/mysql-init.sql --user=mysql & \
    sleep 8 && \
    mysqladmin shutdown -u root 2>/dev/null || true

RUN mkdir -p /var/log/supervisor

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 8080

CMD ["/init-db.sh"]
