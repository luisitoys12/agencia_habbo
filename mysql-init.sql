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
-- TABLA: registro_usuario
-- Columnas usadas por el codigo: id, nombre_usuario, correo,
--   password_registro, rol_id, Rango_asignado, fecha_registro,
--   ip_registro
-- ============================================================
CREATE TABLE IF NOT EXISTS `registro_usuario` (
  `id`                  INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_usuario`      VARCHAR(100) NOT NULL UNIQUE,
  `usuario_registro`    VARCHAR(100) DEFAULT NULL,   -- alias legacy (puede ser igual a nombre_usuario)
  `correo`              VARCHAR(150) DEFAULT NULL,
  `password_registro`   VARCHAR(255) NOT NULL,
  `rol_id`              INT NOT NULL DEFAULT 1,
  `Rango_asignado`      INT NOT NULL DEFAULT 1,
  `fecha_registro`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  `ip_registro`         VARCHAR(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- USUARIO ADMIN INICIAL
-- Usuario: Admin  |  Contrasena: admin2026
-- ============================================================
INSERT IGNORE INTO `registro_usuario`
  (`nombre_usuario`, `usuario_registro`, `password_registro`, `rol_id`, `Rango_asignado`, `ip_registro`)
VALUES
  ('Admin', 'Admin',
   '$2y$12$9z3zQkL5eXv8pYqW1uJhXeO2tRm4nKdVwAsBcDE6FgHiJkLmNoPqR',
   4, 4, '127.0.0.1');

-- ============================================================
-- TABLA: rangos
-- ============================================================
CREATE TABLE IF NOT EXISTS `rangos` (
  `id_rango` INT AUTO_INCREMENT PRIMARY KEY,
  `rango`    VARCHAR(100) NOT NULL,
  `imagen`   VARCHAR(500) DEFAULT NULL,
  `orden`    INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `rangos` (`id_rango`, `rango`, `imagen`, `orden`) VALUES
(1, 'Aprendiz',     NULL, 1),
(2, 'Guardia',      NULL, 2),
(3, 'Veterano',     NULL, 3),
(4, 'Elite',        NULL, 4),
(5, 'Maestro',      NULL, 5),
(6, 'Comandante',   NULL, 6),
(7, 'Lider',        NULL, 7),
(8, 'Dueño',        NULL, 8);

-- ============================================================
-- TABLA: modificar_administradores
-- ============================================================
CREATE TABLE IF NOT EXISTS `modificar_administradores` (
  `id`     INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `rango`  VARCHAR(100) NOT NULL DEFAULT 'Staff',
  `cara`   VARCHAR(50)  NOT NULL DEFAULT 'sml',
  `accion` VARCHAR(50)  NOT NULL DEFAULT 'std',
  `bebida` VARCHAR(50)  NOT NULL DEFAULT 'drk=1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `modificar_administradores` (`nombre`, `rango`, `cara`, `accion`, `bebida`) VALUES
('Admin', 'Dueño', 'sml', 'std', 'drk=1');

-- ============================================================
-- TABLA: modificar_membresias
-- ============================================================
CREATE TABLE IF NOT EXISTS `modificar_membresias` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`     VARCHAR(100) NOT NULL,
  `precio`     INT NOT NULL DEFAULT 0,
  `duracion`   VARCHAR(100) NOT NULL DEFAULT '30 dias',
  `beneficios` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `modificar_membresias` (`id`, `nombre`, `precio`, `duracion`, `beneficios`) VALUES
(1, 'Diamante',         50, '30 dias', 'Acceso VIP completo + badge exclusivo'),
(2, 'Guarda Paga Plus', 35, '15 dias', 'Paga doble + prioridad en eventos'),
(3, 'Level Up',         20, '7 dias',  'XP x2 durante la semana'),
(4, 'Premium',          40, '30 dias', 'Acceso a salas premium + beneficios mensuales'),
(5, 'Regla Libre',      25, '30 dias', 'Sin restricciones de horario'),
(6, 'Guarda Paga',      15, '7 dias',  'Paga semanal garantizada');

-- ============================================================
-- TABLA: publicaciones (noticias)
-- ============================================================
CREATE TABLE IF NOT EXISTS `publicaciones` (
  `id`       INT AUTO_INCREMENT PRIMARY KEY,
  `titulo`   VARCHAR(255) NOT NULL,
  `contenido` TEXT NOT NULL,
  `autor`    VARCHAR(100) NOT NULL DEFAULT 'Admin',
  `imagen`   VARCHAR(500) DEFAULT NULL,
  `fecha`    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `publicaciones` (`titulo`, `contenido`, `autor`) VALUES
('Bienvenidos al verano en Reino Hogwarz!', '¡Esta temporada de verano trae eventos epicos, torneos de playa y mucha diversion. Unete ahora!', 'Admin');

-- ============================================================
-- TABLA: notificaciones
-- ============================================================
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `mensaje`    VARCHAR(500) NOT NULL,
  `leida`      TINYINT(1)  NOT NULL DEFAULT 0,
  `fecha`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_usuario`) REFERENCES `registro_usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: dinero_digital   <-- FALTABA
-- Almacena los creditos virtuales de cada usuario
-- ============================================================
CREATE TABLE IF NOT EXISTS `dinero_digital` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario`  INT NOT NULL UNIQUE,
  `creditos`    INT NOT NULL DEFAULT 0,
  `actualizado` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_usuario`) REFERENCES `registro_usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creditos iniciales del admin
INSERT IGNORE INTO `dinero_digital` (`id_usuario`, `creditos`)
SELECT id, 9999 FROM `registro_usuario` WHERE nombre_usuario = 'Admin' LIMIT 1;

-- ============================================================
-- TABLA: sanciones   <-- FALTABA
-- ============================================================
CREATE TABLE IF NOT EXISTS `sanciones` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `id_sancionado`   INT NOT NULL,
  `nick_sancionado` VARCHAR(100) NOT NULL,
  `id_admin`        INT NOT NULL,
  `nick_admin`      VARCHAR(100) NOT NULL,
  `motivo`          TEXT NOT NULL,
  `tipo`            VARCHAR(100) NOT NULL DEFAULT 'Advertencia',
  `duracion`        VARCHAR(100) NOT NULL DEFAULT 'Indefinida',
  `activa`          TINYINT(1)  NOT NULL DEFAULT 1,
  `fecha`           DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_sancionado`) REFERENCES `registro_usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: actividad_reciente   <-- FALTABA
-- ============================================================
CREATE TABLE IF NOT EXISTS `actividad_reciente` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario`  INT NOT NULL,
  `descripcion` VARCHAR(500) NOT NULL,
  `fecha`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_usuario`) REFERENCES `registro_usuario`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: personas  (sistema legacy)
-- ============================================================
CREATE TABLE IF NOT EXISTS `personas` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`         VARCHAR(100) NOT NULL,
  `contrasena`     VARCHAR(255) NOT NULL,
  `rango`          VARCHAR(100) NOT NULL DEFAULT 'Aprendiz',
  `cara`           VARCHAR(50)  NOT NULL DEFAULT 'sml',
  `accion`         VARCHAR(50)  NOT NULL DEFAULT 'std',
  `bebida`         VARCHAR(50)  NOT NULL DEFAULT 'drk=1',
  `monedas`        INT NOT NULL DEFAULT 0,
  `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: membresias_usuarios
-- ============================================================
CREATE TABLE IF NOT EXISTS `membresias_usuarios` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `id_persona`   INT NOT NULL,
  `id_membresia` INT NOT NULL,
  `fecha_inicio` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin`    DATETIME,
  FOREIGN KEY (`id_persona`)   REFERENCES `personas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_membresia`) REFERENCES `modificar_membresias`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: ventas
-- ============================================================
CREATE TABLE IF NOT EXISTS `ventas` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `id_persona` INT NOT NULL,
  `producto`   VARCHAR(200) NOT NULL,
  `cantidad`   INT NOT NULL DEFAULT 1,
  `precio`     INT NOT NULL DEFAULT 0,
  `fecha`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: pagas
-- ============================================================
CREATE TABLE IF NOT EXISTS `pagas` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `id_persona` INT NOT NULL,
  `monto`      INT NOT NULL DEFAULT 0,
  `concepto`   VARCHAR(200) DEFAULT 'Paga semanal',
  `fecha`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_persona`) REFERENCES `personas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
