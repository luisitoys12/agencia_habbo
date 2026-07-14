#!/bin/bash
set -e

echo '>>> Iniciando MySQL...'
service mariadb start

# Esperar a que MySQL esté listo
for i in $(seq 1 30); do
    if mysqladmin ping --silent 2>/dev/null; then
        echo '>>> MySQL listo.'
        break
    fi
    echo "Esperando MySQL... intento $i"
    sleep 2
done

# Crear DB, usuario y permisos
mysql -u root <<-EOSQL
    CREATE DATABASE IF NOT EXISTS sistema_agencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS 'agencia_user'@'localhost' IDENTIFIED BY 'agencia_pass2026';
    CREATE USER IF NOT EXISTS 'agencia_user'@'127.0.0.1' IDENTIFIED BY 'agencia_pass2026';
    GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'localhost';
    GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'127.0.0.1';
    FLUSH PRIVILEGES;
EOSQL

# Importar schema principal
if [ -f /var/www/html/base-datos/tablas/sistema_agencia.sql ]; then
    echo '>>> Importando schema...'
    mysql -u root sistema_agencia < /var/www/html/base-datos/tablas/sistema_agencia.sql
    echo '>>> Schema importado.'
fi

# Importar permisos
if [ -f /var/www/html/base-datos/permisos/grants_usuarios_basicos.sql ]; then
    echo '>>> Importando permisos...'
    mysql -u root sistema_agencia < /var/www/html/base-datos/permisos/grants_usuarios_basicos.sql
    echo '>>> Permisos importados.'
fi

service mariadb stop
echo '>>> Arrancando servicios con supervisord...'
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
