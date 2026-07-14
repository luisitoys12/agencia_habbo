#!/bin/bash
set -e

# Iniciar MySQL temporalmente para crear la DB
service mariadb start

# Esperar a que MySQL esté listo
until mysqladmin ping --silent; do
    echo 'Esperando MySQL...'
    sleep 1
done

# Crear base de datos y usuario
mysql -u root <<-EOSQL
    CREATE DATABASE IF NOT EXISTS agencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS 'agencia_user'@'localhost' IDENTIFIED BY 'agencia_pass';
    GRANT ALL PRIVILEGES ON agencia.* TO 'agencia_user'@'localhost';
    FLUSH PRIVILEGES;
EOSQL

# Si existe un SQL de inicialización, importarlo
if [ -f /var/www/html/agencia.sql ]; then
    mysql -u root agencia < /var/www/html/agencia.sql
    echo 'Base de datos importada.'
fi

service mariadb stop

# Iniciar todos los servicios con supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
