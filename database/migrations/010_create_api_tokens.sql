-- Migración: Crear tabla api_tokens para autenticación API móvil
-- Fecha: 2024

CREATE TABLE IF NOT EXISTS api_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(500) NOT NULL COMMENT 'Hash SHA256 del token JWT',
    device_name VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del dispositivo',
    device_info TEXT DEFAULT NULL COMMENT 'JSON con info del dispositivo',
    last_used_at DATETIME DEFAULT NULL COMMENT 'Última vez que se usó el token',
    expires_at DATETIME NOT NULL COMMENT 'Fecha de expiración',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_token (token(255)),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columnas de geofence a work_centers si no existen
ALTER TABLE work_centers 
    ADD COLUMN IF NOT EXISTS geofence_radius INT DEFAULT 100 COMMENT 'Radio permitido en metros para fichar',
    ADD COLUMN IF NOT EXISTS qr_token VARCHAR(255) DEFAULT NULL COMMENT 'Token único para código QR';

-- Generar QR tokens únicos para centros de trabajo existentes
UPDATE work_centers 
SET qr_token = CONCAT('wc_', MD5(CONCAT(id, NOW(), RAND()))) 
WHERE qr_token IS NULL;

-- Agregar columnas de validación de turno a time_entries
ALTER TABLE time_entries
    ADD COLUMN IF NOT EXISTS shift_id INT UNSIGNED DEFAULT NULL COMMENT 'Turno asignado al momento del fichaje',
    ADD COLUMN IF NOT EXISTS shift_validation_status VARCHAR(20) DEFAULT NULL COMMENT 'on_time, late, early, overtime, unassigned',
    ADD COLUMN IF NOT EXISTS shift_validation_message VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS late_minutes INT DEFAULT NULL COMMENT 'Minutos de retraso',
    ADD COLUMN IF NOT EXISTS early_minutes INT DEFAULT NULL COMMENT 'Minutos de anticipación',
    ADD COLUMN IF NOT EXISTS overtime_minutes INT DEFAULT NULL COMMENT 'Minutos extra trabajados';
