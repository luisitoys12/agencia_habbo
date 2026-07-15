-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_agencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_agencia;

-- Crear usuario de aplicacion
CREATE USER IF NOT EXISTS 'agencia_user'@'localhost' IDENTIFIED BY 'agencia_pass2026';
CREATE USER IF NOT EXISTS 'agencia_user'@'127.0.0.1' IDENTIFIED BY 'agencia_pass2026';
GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'localhost';
GRANT ALL PRIVILEGES ON sistema_agencia.* TO 'agencia_user'@'127.0.0.1';
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;

-- ============================================================
-- TABLA: registro_usuario (login/register)
-- ============================================================
CREATE TABLE IF NOT EXISTS `registro_usuario` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_registro` VARCHAR(100) NOT NULL UNIQUE,
  `password_registro` VARCHAR(255) NOT NULL,
  `rol_id` INT NOT NULL DEFAULT 1,
  `Rango_asignado` INT NOT NULL DEFAULT 1,
  `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `ip_registro` VARCHAR(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: modificar_administradores
-- ============================================================
CREATE TABLE IF NOT EXISTS `modificar_administradores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `rango` VARCHAR(100) NOT NULL DEFAULT 'Staff',
  `cara` VARCHAR(50) NOT NULL DEFAULT 'sml',
  `accion` VARCHAR(50) NOT NULL DEFAULT 'std',
  `bebida` VARCHAR(50) NOT NULL DEFAULT 'drk=1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `modificar_administradores` (`nombre`, `rango`, `cara`, `accion`, `bebida`) VALUES
('Admin', 'Dueño', 'sml', 'std', 'drk=1');

-- ============================================================
-- TABLA: modificar_membresias
-- ============================================================
CREATE TABLE IF NOT EXISTS `modificar_membresias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `precio` INT NOT NULL DEFAULT 0,
  `duracion` VARCHAR(100) NOT NULL DEFAULT '30 días',
  `beneficios` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `modificar_membresias` (`nombre`, `precio`, `duracion`, `beneficios`) VALUES
('Diamante', 50, '30 días', 'Acceso VIP completo + badge exclusivo'),
('Guarda Paga Plus', 35, '15 días', 'Paga doble + prioridad en eventos'),
('Level Up', 20, '7 días', 'XP x2 durante la semana'),
('Premium', 40, '30 días', 'Acceso a salas premium + beneficios mensuales'),
('Regla Libre', 25, '30 días', 'Sin restricciones de horario'),
('Guarda Paga', 15, '7 días', 'Paga semanal garantizada');

-- ============================================================
-- TABLA: personas
-- ============================================================
CREATE TABLE IF NOT EXISTS `personas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `rango` VARCHAR(100) NOT NULL DEFAULT 'Aprendiz',
  `cara` VARCHAR(50) NOT NULL DEFAULT 'sml',
  `accion` VARCHAR(50) NOT NULL DEFAULT 'std',
  `bebida` VARCHAR(50) NOT NULL DEFAULT 'drk=1',
  `monedas` INT NOT NULL DEFAULT 0,
  `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: membresias_usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS `membresias_usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_persona` INT NOT NULL,
  `id_membresia` INT NOT NULL,
  `fecha_inicio` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` DATETIME,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_membresia`) REFERENCES `modificar_membresias`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: ventas
-- ============================================================
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_persona` INT NOT NULL,
  `producto` VARCHAR(200) NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `precio` INT NOT NULL DEFAULT 0,
  `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: pagas
-- ============================================================
CREATE TABLE IF NOT EXISTS `pagas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_persona` INT NOT NULL,
  `monto` INT NOT NULL DEFAULT 0,
  `concepto` VARCHAR(200) DEFAULT 'Paga semanal',
  `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: publicaciones
-- ============================================================
CREATE TABLE IF NOT EXISTS `publicaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `contenido` TEXT NOT NULL,
  `autor` VARCHAR(100) NOT NULL DEFAULT 'Admin',
  `imagen` VARCHAR(500) DEFAULT NULL,
  `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `publicaciones` (`titulo`, `contenido`, `autor`) VALUES
('¡Bienvenidos al Reino Hogwartz!', 'Esta es la agencia oficial. Únete y convíértete en un mago.', 'Admin');
