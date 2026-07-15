#!/bin/bash
set -e

echo '>>> [1/4] Arrancando MariaDB temporal para verificar/restaurar grants...'
mysqld_safe --user=mysql --skip-networking=0 &
MYSQL_PID=$!

# Esperar a que MySQL este listo (hasta 60s)
for i in $(seq 1 30); do
    if mysqladmin ping -u root --silent 2>/dev/null; then
        echo ">>> MySQL listo en intento $i."
        break
    fi
    echo "Esperando MySQL... intento $i"
    sleep 2
done

# Re-aplicar siempre el usuario y grants en cada arranque
# Esto soluciona el problema de que los grants se pierden entre reinicios
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
echo '>>> Grants aplicados correctamente.'

# Importar schema si existe y la tabla principal aun no tiene datos
if [ -f /var/www/html/base-datos/tablas/sistema_agencia.sql ]; then
    TABLE_EXISTS=$(mysql -u root sistema_agencia -e "SHOW TABLES LIKE 'registro_usuario';" 2>/dev/null | wc -l)
    if [ "$TABLE_EXISTS" -lt "1" ]; then
        echo '>>> [3/4] Importando schema inicial...'
        mysql -u root sistema_agencia < /var/www/html/base-datos/tablas/sistema_agencia.sql
        echo '>>> Schema importado.'
    else
        echo '>>> [3/4] Schema ya existe, saltando importacion.'
    fi
fi

# Verificar que el usuario puede conectarse antes de arrancar Apache
echo '>>> Verificando conexion de agencia_user...'
for i in $(seq 1 10); do
    if mysql -u agencia_user -pagencia_pass2026 -h 127.0.0.1 sistema_agencia -e 'SELECT 1;' >/dev/null 2>&1; then
        echo ">>> Conexion de agencia_user verificada en intento $i. OK."
        break
    fi
    echo ">>> Reintentando verificacion... intento $i"
    sleep 1
done

# Apagar MySQL temporal
mysqladmin shutdown -u root 2>/dev/null || true
sleep 3

echo '>>> [4/4] Arrancando todos los servicios con supervisord...'
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
