-- Migración para permitir tenant_id nullable en users
-- Esto permite que usuarios se registren sin empresa asignada

ALTER TABLE users 
MODIFY COLUMN tenant_id INT UNSIGNED NULL COMMENT 'ID de la empresa (nullable para usuarios sin asignar)';

-- Eliminar la constraint unique si existe
ALTER TABLE users
DROP INDEX IF EXISTS unique_tenant_email;

-- Crear una nueva constraint que permita NULLs
ALTER TABLE users
ADD CONSTRAINT unique_tenant_email UNIQUE KEY (tenant_id, email);
