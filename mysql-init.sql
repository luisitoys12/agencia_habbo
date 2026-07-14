-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_agencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario de aplicacion
CREATE USER IF NOT EXISTS 'agencia_user'@'localhost' IDENTIFIED BY 'agencia_pass2026';
CREATE USER IF NOT EXISTS 'agencia_user'@'127.0.0.1' IDENTIFIED BY 'agencia_pass2026';
GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'localhost';
GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'127.0.0.1';

-- Permitir root sin password por TCP
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;
