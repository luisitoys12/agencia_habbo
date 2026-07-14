#!/bin/bash
set -e

echo '>>> Arrancando MariaDB...'
mysqld_safe --user=mysql &
MYSQL_PID=$!

# Esperar a que MySQL esté listo
for i in $(seq 1 30); do
    if mysqladmin ping -u root --silent 2>/dev/null; then
        echo ">>> MySQL listo en intento $i."
        break
    fi
    echo "Esperando MySQL... intento $i"
    sleep 2
done

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

# Detener MySQL temporal
mysqladmin shutdown -u root || true
sleep 2

echo '>>> Arrancando todos los servicios con supervisord...'
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
