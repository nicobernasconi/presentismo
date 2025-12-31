-- =====================================================
-- Migración: Soporte para Turnos Nocturnos
-- Fecha: 2025-12-29
-- Descripción: Permite definir bloques que cruzan medianoche
--              Ejemplo: Lunes 21:00 → Martes 06:00
-- =====================================================

-- Agregar columna para indicar si el bloque termina al día siguiente
ALTER TABLE shift_time_blocks
ADD COLUMN spans_next_day TINYINT(1) DEFAULT 0 
COMMENT 'Indica si el bloque cruza medianoche y termina al día siguiente'
AFTER end_time;

-- Actualizar comentarios de la tabla
ALTER TABLE shift_time_blocks
COMMENT = 'Bloques de tiempo por día. Soporta turnos nocturnos con spans_next_day=1 para cruces de medianoche';
