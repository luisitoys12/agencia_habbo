-- ============================================================
-- NUEVAS TABLAS requeridas por el panel completo v3
-- Ejecutar UNA SOLA VEZ en tu base de datos
-- ============================================================

-- Feed de actividad reciente
CREATE TABLE IF NOT EXISTS actividad_reciente (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario  INT NOT NULL,
    descripcion VARCHAR(500) NOT NULL,
    fecha       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (fecha),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sanciones
CREATE TABLE IF NOT EXISTS sanciones (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_sancionado   INT NOT NULL,
    id_autor        INT NOT NULL,
    tipo_sancion    ENUM('advertencia','suspension_temporal','suspension_permanente','degradacion') NOT NULL DEFAULT 'advertencia',
    motivo          VARCHAR(500) NOT NULL,
    fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activa          TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_sancionado (id_sancionado),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
