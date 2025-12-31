-- =====================================================
-- Sistema de Presentismo - Datos Iniciales (Seeders)
-- =====================================================

USE presentismo_db;

-- =====================================================
-- Roles del sistema
-- =====================================================
INSERT INTO roles (id, name, slug, description, level, is_system) VALUES
(1, 'Super Administrador', 'super_admin', 'Acceso total al sistema, gestión de todos los tenants', 1, 1),
(2, 'Administrador', 'admin', 'Administrador de empresa/tenant', 10, 1),
(3, 'Supervisor', 'supervisor', 'Supervisor de equipo/departamento', 50, 1),
(4, 'Empleado', 'employee', 'Usuario estándar', 100, 1);

-- =====================================================
-- Permisos del sistema
-- =====================================================
INSERT INTO permissions (name, slug, module) VALUES
-- Dashboard
('Ver dashboard', 'dashboard.view', 'dashboard'),
('Ver estadísticas globales', 'dashboard.stats', 'dashboard'),

-- Usuarios/Empleados
('Ver empleados', 'users.view', 'users'),
('Crear empleados', 'users.create', 'users'),
('Editar empleados', 'users.edit', 'users'),
('Eliminar empleados', 'users.delete', 'users'),
('Ver empleados del equipo', 'users.view_team', 'users'),

-- Fichajes
('Ver fichajes propios', 'time_entries.view_own', 'time_entries'),
('Ver fichajes del equipo', 'time_entries.view_team', 'time_entries'),
('Ver todos los fichajes', 'time_entries.view_all', 'time_entries'),
('Registrar fichaje', 'time_entries.create', 'time_entries'),
('Editar fichajes', 'time_entries.edit', 'time_entries'),
('Aprobar fichajes', 'time_entries.approve', 'time_entries'),
('Eliminar fichajes', 'time_entries.delete', 'time_entries'),

-- Ausencias
('Ver ausencias propias', 'absences.view_own', 'absences'),
('Ver ausencias del equipo', 'absences.view_team', 'absences'),
('Ver todas las ausencias', 'absences.view_all', 'absences'),
('Solicitar ausencia', 'absences.create', 'absences'),
('Aprobar ausencias', 'absences.approve', 'absences'),
('Eliminar ausencias', 'absences.delete', 'absences'),

-- Turnos
('Ver turnos', 'shifts.view', 'shifts'),
('Gestionar turnos', 'shifts.manage', 'shifts'),
('Asignar turnos', 'shifts.assign', 'shifts'),

-- Proyectos
('Ver proyectos', 'projects.view', 'projects'),
('Gestionar proyectos', 'projects.manage', 'projects'),
('Ver todos los proyectos', 'projects.view_all', 'projects'),

-- Tareas
('Ver tareas', 'tasks.view', 'tasks'),
('Gestionar tareas', 'tasks.manage', 'tasks'),

-- Departamentos
('Ver departamentos', 'departments.view', 'departments'),
('Gestionar departamentos', 'departments.manage', 'departments'),

-- Centros de trabajo
('Ver centros de trabajo', 'work_centers.view', 'work_centers'),
('Gestionar centros de trabajo', 'work_centers.manage', 'work_centers'),

-- Reportes
('Ver reportes propios', 'reports.view_own', 'reports'),
('Ver reportes del equipo', 'reports.view_team', 'reports'),
('Ver todos los reportes', 'reports.view_all', 'reports'),
('Exportar reportes', 'reports.export', 'reports'),

-- Configuración
('Ver configuración', 'settings.view', 'settings'),
('Gestionar configuración', 'settings.manage', 'settings'),

-- Auditoría
('Ver logs de auditoría', 'audit.view', 'audit');

-- =====================================================
-- Asignar permisos a roles
-- =====================================================

-- Super Admin: todos los permisos
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- Admin: casi todos excepto gestión de tenants
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions 
WHERE slug NOT IN ('audit.view');

-- Supervisor: permisos de equipo
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions 
WHERE slug IN (
    'dashboard.view',
    'users.view_team',
    'time_entries.view_own', 'time_entries.view_team', 'time_entries.create', 'time_entries.approve',
    'absences.view_own', 'absences.view_team', 'absences.create', 'absences.approve',
    'shifts.view', 'shifts.assign',
    'projects.view',
    'tasks.view', 'tasks.manage',
    'departments.view',
    'work_centers.view',
    'reports.view_own', 'reports.view_team'
);

-- Empleado: permisos básicos propios
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions 
WHERE slug IN (
    'dashboard.view',
    'time_entries.view_own', 'time_entries.create',
    'absences.view_own', 'absences.create',
    'shifts.view',
    'projects.view',
    'tasks.view',
    'reports.view_own'
);

-- =====================================================
-- Tipos de ausencia predeterminados
-- =====================================================
INSERT INTO absence_types (tenant_id, name, code, color, is_paid, requires_approval, requires_document, max_days_per_year) VALUES
(NULL, 'Vacaciones', 'VAC', '#10B981', 1, 1, 0, 22),
(NULL, 'Enfermedad', 'ENF', '#EF4444', 1, 1, 1, NULL),
(NULL, 'Permiso personal', 'PER', '#F59E0B', 1, 1, 0, 6),
(NULL, 'Asuntos propios', 'ASU', '#8B5CF6', 1, 1, 0, 3),
(NULL, 'Maternidad/Paternidad', 'MAT', '#EC4899', 1, 1, 1, NULL),
(NULL, 'Matrimonio', 'MAT', '#06B6D4', 1, 1, 1, 15),
(NULL, 'Fallecimiento familiar', 'FAL', '#6B7280', 1, 0, 0, 5),
(NULL, 'Mudanza', 'MUD', '#84CC16', 1, 1, 0, 1),
(NULL, 'Visita médica', 'MED', '#3B82F6', 1, 1, 1, NULL),
(NULL, 'Formación', 'FOR', '#F97316', 1, 1, 0, NULL),
(NULL, 'Teletrabajo', 'TEL', '#14B8A6', 1, 0, 0, NULL),
(NULL, 'Ausencia no justificada', 'ANJ', '#DC2626', 0, 0, 0, NULL);

-- =====================================================
-- Tenant demo y usuario admin
-- =====================================================
INSERT INTO tenants (id, name, slug, email, phone, is_active) VALUES
(1, 'Empresa Demo', 'demo', 'admin@demo.com', '+34 600 000 000', 1);

-- Usuario Super Admin (contraseña: admin123)
INSERT INTO users (
    tenant_id, role_id, email, password, name, first_name, last_name, 
    employee_code, is_active, email_verified_at
) VALUES (
    1, 1, 'admin@presentismo.local', 
    '$2y$12$LQv3c1Yqr9JGOnHzq5lYEuOMpXvBKQ9hXLQJZ5kKvN.gN8FZJxMnO', -- admin123
    'Super Administrador', 'Super', 'Admin',
    'ADMIN001', 1, NOW()
);

-- Departamentos demo
INSERT INTO departments (tenant_id, name, code, description) VALUES
(1, 'Dirección', 'DIR', 'Dirección general de la empresa'),
(1, 'Recursos Humanos', 'RRHH', 'Departamento de recursos humanos'),
(1, 'Tecnología', 'TEC', 'Departamento de tecnología e informática'),
(1, 'Comercial', 'COM', 'Departamento comercial y ventas'),
(1, 'Administración', 'ADM', 'Departamento de administración y finanzas');

-- Centro de trabajo demo
INSERT INTO work_centers (tenant_id, name, code, address, city, postal_code, latitude, longitude, radius) VALUES
(1, 'Oficina Central', 'OFC-01', 'Calle Principal 123', 'Madrid', '28001', 40.416775, -3.703790, 150);

-- Turnos demo
INSERT INTO shifts (tenant_id, name, code, color, start_time, end_time, break_duration, working_days) VALUES
(1, 'Jornada Completa', 'JC', '#3B82F6', '09:00:00', '18:00:00', 60, '[1,2,3,4,5]'),
(1, 'Turno Mañana', 'TM', '#10B981', '07:00:00', '15:00:00', 30, '[1,2,3,4,5]'),
(1, 'Turno Tarde', 'TT', '#F59E0B', '15:00:00', '23:00:00', 30, '[1,2,3,4,5]'),
(1, 'Jornada Intensiva', 'JI', '#8B5CF6', '08:00:00', '15:00:00', 30, '[1,2,3,4,5]');

-- Proyecto demo
INSERT INTO projects (tenant_id, name, code, description, status, is_billable) VALUES
(1, 'Proyecto General', 'GEN', 'Proyecto general para horas no asignadas', 'active', 0),
(1, 'Desarrollo Web', 'DEV-WEB', 'Proyecto de desarrollo web', 'active', 1);

-- =====================================================
-- Festivos nacionales España 2025
-- =====================================================
INSERT INTO calendar_events (tenant_id, name, date, type) VALUES
(NULL, 'Año Nuevo', '2025-01-01', 'holiday'),
(NULL, 'Reyes Magos', '2025-01-06', 'holiday'),
(NULL, 'Viernes Santo', '2025-04-18', 'holiday'),
(NULL, 'Día del Trabajo', '2025-05-01', 'holiday'),
(NULL, 'Asunción de la Virgen', '2025-08-15', 'holiday'),
(NULL, 'Fiesta Nacional de España', '2025-10-12', 'holiday'),
(NULL, 'Todos los Santos', '2025-11-01', 'holiday'),
(NULL, 'Día de la Constitución', '2025-12-06', 'holiday'),
(NULL, 'Inmaculada Concepción', '2025-12-08', 'holiday'),
(NULL, 'Navidad', '2025-12-25', 'holiday');
