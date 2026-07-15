#!/bin/bash
# Esperar a que MariaDB acepte conexiones del usuario de la app
# antes de arrancar Apache. Evita el "Connection refused".

echo '[apache-wait] Esperando a que MariaDB acepte conexiones...'

# Primer paso: esperar que el puerto 3306 este abierto
for i in $(seq 1 30); do
    if bash -c 'cat < /dev/null > /dev/tcp/127.0.0.1/3306' 2>/dev/null; then
        echo "[apache-wait] Puerto 3306 abierto en intento $i."
        break
    fi
    echo "[apache-wait] Puerto 3306 cerrado... intento $i/30"
    sleep 2
done

# Segundo paso: esperar que agencia_user pueda autenticarse
for i in $(seq 1 20); do
    if mysql -u agencia_user -pagencia_pass2026 -h 127.0.0.1 sistema_agencia \
       -e 'SELECT 1;' >/dev/null 2>&1; then
        echo "[apache-wait] agencia_user autenticado OK en intento $i. Arrancando Apache."
        break
    fi
    echo "[apache-wait] Esperando autenticacion... intento $i/20"
    sleep 2
done

exec /usr/sbin/apache2ctl -D FOREGROUND
