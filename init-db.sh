#!/bin/bash
set -e

echo '=== [RUNTIME INIT] Verificando MariaDB datadir...'

# Si el datadir esta vacio o no tiene los archivos de sistema, reinicializar
if [ ! -f /var/lib/mysql/ibdata1 ]; then
    echo '>>> Datadir vacio o incompleto. Reinicializando...'
    rm -rf /var/lib/mysql/*
    chown -R mysql:mysql /var/lib/mysql
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql
fi

echo '>>> [1/4] Arrancando MariaDB para verificar grants...'
mysqld_safe --user=mysql --skip-networking=0 &

# Esperar a que MySQL este listo (hasta 60s)
for i in $(seq 1 30); do
    if mysqladmin ping -u root --silent 2>/dev/null; then
        echo ">>> MySQL listo en intento $i."
        break
    fi
    echo "Esperando MySQL... intento $i/30"
    sleep 2
done

# Verificar que podemos conectarnos como root
if ! mysqladmin ping -u root --silent 2>/dev/null; then
    echo 'ERROR: MySQL no responde despues de 60s. Abortando.'
    exit 1
fi

# Re-aplicar siempre el usuario y grants en cada arranque
echo '>>> [2/4] Reaplicando usuario y permisos...'
mysql -u root <<-EOSQL
    CREATE DATABASE IF NOT EXISTS sistema_agencia
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_unicode_ci;
    CREATE USER IF NOT EXISTS 'agencia_user'@'localhost'
        IDENTIFIED BY 'agencia_pass2026';
    CREATE USER IF NOT EXISTS 'agencia_user'@'127.0.0.1'
        IDENTIFIED BY 'agencia_pass2026';
    GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'localhost';
    GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'127.0.0.1';
    ALTER USER 'agencia_user'@'localhost' IDENTIFIED BY 'agencia_pass2026';
    ALTER USER 'agencia_user'@'127.0.0.1' IDENTIFIED BY 'agencia_pass2026';
    FLUSH PRIVILEGES;
EOSQL
echo '>>> Grants aplicados.'

# Importar schema si la tabla principal no existe
TABLE_EXISTS=$(mysql -u root sistema_agencia -e "SHOW TABLES LIKE 'registro_usuario';" 2>/dev/null | wc -l)
if [ "$TABLE_EXISTS" -lt "1" ]; then
    echo '>>> [3/4] Importando schema + datos iniciales...'
    mysql -u root sistema_agencia < /mysql-init.sql 2>/dev/null || \
    mysql -u root < /mysql-init.sql
    echo '>>> Schema importado.'
else
    echo '>>> [3/4] Schema ya existe, saltando.'
fi

# Verificar que agencia_user puede conectarse
echo '>>> Verificando conexion de agencia_user...'
for i in $(seq 1 10); do
    if mysql -u agencia_user -pagencia_pass2026 -h 127.0.0.1 sistema_agencia \
       -e 'SELECT 1;' >/dev/null 2>&1; then
        echo ">>> agencia_user OK en intento $i."
        break
    fi
    echo ">>> Reintentando verificacion... intento $i/10"
    sleep 1
done

# Apagar MySQL temporal
mysqladmin shutdown -u root 2>/dev/null || true
sleep 3

echo '>>> [4/4] Lanzando supervisord...'
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
