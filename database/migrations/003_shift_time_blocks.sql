-- =====================================================
-- Migración: Sistema de Turnos con Bloques de Tiempo
-- Fecha: 2025-12-29
-- Descripción: Replantea el sistema de turnos para permitir
--              definir múltiples bloques de tiempo por día
-- =====================================================

-- Crear tabla para bloques de tiempo de turnos
CREATE TABLE IF NOT EXISTS shift_time_blocks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shift_id INT UNSIGNED NOT NULL,
    day_of_week TINYINT UNSIGNED NOT NULL COMMENT '1=Lunes, 7=Domingo',
    start_time TIME NOT NULL COMMENT 'Hora de inicio del bloque',
    end_time TIME NOT NULL COMMENT 'Hora de fin del bloque',
    block_type ENUM('work', 'break') DEFAULT 'work' COMMENT 'Tipo de bloque: trabajo o descanso',
    order_index TINYINT UNSIGNED DEFAULT 0 COMMENT 'Orden de los bloques en el día',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    INDEX idx_shift_day (shift_id, day_of_week),
    INDEX idx_day_time (day_of_week, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bloques de tiempo por día para cada turno';

-- Agregar columna para indicar si el turno usa bloques de tiempo
ALTER TABLE shifts
ADD COLUMN use_time_blocks TINYINT(1) DEFAULT 0 
COMMENT 'Si es 1 usa shift_time_blocks si es 0 usa start_time/end_time tradicional'
AFTER is_active;

-- Crear vista para facilitar consultas de horarios
CREATE OR REPLACE VIEW v_shift_schedules AS
SELECT 
    s.id as shift_id,
    s.tenant_id,
    s.name as shift_name,
    s.color,
    s.use_time_blocks,
    -- Para turnos tradicionales
    s.start_time as traditional_start,
    s.end_time as traditional_end,
    s.working_days as traditional_days,
    -- Para turnos con bloques
    stb.id as block_id,
    stb.day_of_week,
    stb.start_time as block_start,
    stb.end_time as block_end,
    stb.block_type,
    stb.order_index,
    CASE stb.day_of_week
        WHEN 1 THEN 'Lunes'
        WHEN 2 THEN 'Martes'
        WHEN 3 THEN 'Miércoles'
        WHEN 4 THEN 'Jueves'
        WHEN 5 THEN 'Viernes'
        WHEN 6 THEN 'Sábado'
        WHEN 7 THEN 'Domingo'
    END as day_name
FROM shifts s
LEFT JOIN shift_time_blocks stb ON s.id = stb.shift_id
WHERE s.deleted_at IS NULL
ORDER BY s.id, stb.day_of_week, stb.order_index;

-- Comentarios para documentación
ALTER TABLE shift_time_blocks 
COMMENT = 'Permite definir horarios fragmentados por día de la semana. Ejemplo: Lunes 11:00-15:00, Martes 11:00-14:00 y 16:00-18:00';
