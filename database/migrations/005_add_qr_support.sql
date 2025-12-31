-- Migración: Agregar soporte para QR en fichaje
-- Ejecutar en la base de datos de producción

-- Agregar columna qr_token a tenants
ALTER TABLE tenants 
ADD COLUMN IF NOT EXISTS qr_token VARCHAR(64) NULL UNIQUE AFTER is_active;

-- Agregar columna pin a users para el fichaje por QR
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS pin VARCHAR(10) NULL AFTER password;

-- Agregar columna source a time_entries para identificar fichajes por QR
ALTER TABLE time_entries 
ADD COLUMN IF NOT EXISTS source ENUM('web', 'mobile', 'qr', 'api') DEFAULT 'web' AFTER ip_address;

-- Índice para búsqueda rápida por qr_token
CREATE INDEX IF NOT EXISTS idx_tenants_qr_token ON tenants(qr_token);
