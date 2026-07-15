#!/bin/bash
# Esperar a que MariaDB acepte conexiones antes de arrancar Apache
# Esto evita el "Connection refused" cuando PHP intenta conectar

echo '[apache-wait] Esperando a que MariaDB este lista...'

MAX=30
for i in $(seq 1 $MAX); do
    if mysqladmin ping -h 127.0.0.1 -u agencia_user -pagencia_pass2026 --silent 2>/dev/null; then
        echo "[apache-wait] MariaDB lista en intento $i. Arrancando Apache..."
        break
    fi
    echo "[apache-wait] MySQL no listo aun... intento $i/$MAX"
    sleep 2
done

# Verificacion extra: esperar que el socket TCP este abierto
for i in $(seq 1 10); do
    if bash -c 'cat < /dev/null > /dev/tcp/127.0.0.1/3306' 2>/dev/null; then
        echo '[apache-wait] Puerto 3306 abierto. OK.'
        break
    fi
    sleep 1
done

exec /usr/sbin/apache2ctl -D FOREGROUND
