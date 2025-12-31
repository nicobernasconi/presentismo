-- =====================================================
-- Sistema de Presentismo - Migración Principal
-- Base de datos: presentismo_db
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS presentismo_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE presentismo_db;

-- =====================================================
-- TABLA: tenants (Empresas/Organizaciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS tenants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Nombre de la empresa',
    slug VARCHAR(100) UNIQUE NOT NULL COMMENT 'Identificador URL-friendly',
    tax_id VARCHAR(20) NULL COMMENT 'CIF/NIF',
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    country VARCHAR(2) DEFAULT 'ES',
    logo_path VARCHAR(255) NULL,
    settings JSON NULL COMMENT 'Configuración personalizada',
    subscription_plan ENUM('free', 'basic', 'professional', 'enterprise') DEFAULT 'free',
    subscription_ends_at DATETIME NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    INDEX idx_tenants_slug (slug),
    INDEX idx_tenants_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: roles
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL COMMENT 'Nombre del rol',
    slug VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255) NULL,
    level INT UNSIGNED DEFAULT 100 COMMENT 'Nivel de privilegio (menor = más privilegios)',
    is_system TINYINT(1) DEFAULT 0 COMMENT 'Rol del sistema no editable',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: permissions
-- =====================================================
CREATE TABLE IF NOT EXISTS permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    module VARCHAR(50) NOT NULL COMMENT 'Módulo al que pertenece',
    description VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: role_permissions
-- =====================================================
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: departments (Departamentos)
-- =====================================================
CREATE TABLE IF NOT EXISTS departments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    parent_id INT UNSIGNED NULL COMMENT 'Para jerarquía de departamentos',
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NULL,
    description TEXT NULL,
    manager_id INT UNSIGNED NULL COMMENT 'ID del responsable',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_departments_tenant (tenant_id),
    INDEX idx_departments_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: work_centers (Centros de Trabajo)
-- =====================================================
CREATE TABLE IF NOT EXISTS work_centers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    country VARCHAR(2) DEFAULT 'ES',
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    radius INT UNSIGNED DEFAULT 100 COMMENT 'Radio permitido en metros para geofencing',
    timezone VARCHAR(50) DEFAULT 'Europe/Madrid',
    requires_geolocation TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    INDEX idx_work_centers_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: users (Usuarios/Empleados)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    department_id INT UNSIGNED NULL,
    work_center_id INT UNSIGNED NULL COMMENT 'Centro de trabajo principal',
    role_id INT UNSIGNED NOT NULL,
    
    -- Datos de acceso
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    
    -- Datos personales
    employee_code VARCHAR(50) NULL COMMENT 'Código de empleado',
    name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    dni VARCHAR(20) NULL COMMENT 'DNI/NIE',
    phone VARCHAR(20) NULL,
    mobile VARCHAR(20) NULL,
    birth_date DATE NULL,
    gender ENUM('M', 'F', 'O') NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    avatar_path VARCHAR(255) NULL,
    
    -- Datos laborales
    position VARCHAR(100) NULL COMMENT 'Puesto de trabajo',
    hire_date DATE NULL COMMENT 'Fecha de alta',
    termination_date DATE NULL COMMENT 'Fecha de baja',
    contract_type ENUM('indefinido', 'temporal', 'practicas', 'formacion', 'otro') NULL,
    hours_per_week DECIMAL(5,2) DEFAULT 40.00,
    
    -- Configuración
    settings JSON NULL,
    language VARCHAR(5) DEFAULT 'es',
    
    -- Control
    is_active TINYINT(1) DEFAULT 1,
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (work_center_id) REFERENCES work_centers(id) ON DELETE SET NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_tenant_email (tenant_id, email),
    UNIQUE KEY unique_tenant_employee_code (tenant_id, employee_code),
    INDEX idx_users_tenant (tenant_id),
    INDEX idx_users_department (department_id),
    INDEX idx_users_role (role_id),
    INDEX idx_users_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar FK para manager_id en departments
ALTER TABLE departments
ADD CONSTRAINT fk_departments_manager
FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL;

-- =====================================================
-- TABLA: shifts (Turnos)
-- =====================================================
CREATE TABLE IF NOT EXISTS shifts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL COMMENT 'Nombre del turno',
    code VARCHAR(20) NULL,
    color VARCHAR(7) DEFAULT '#3B82F6' COMMENT 'Color para UI',
    start_time TIME NOT NULL COMMENT 'Hora de entrada',
    end_time TIME NOT NULL COMMENT 'Hora de salida',
    break_duration INT UNSIGNED DEFAULT 0 COMMENT 'Duración del descanso en minutos',
    break_start_time TIME NULL COMMENT 'Hora de inicio del descanso',
    is_flexible TINYINT(1) DEFAULT 0 COMMENT 'Horario flexible',
    flexible_margin INT UNSIGNED DEFAULT 0 COMMENT 'Margen de flexibilidad en minutos',
    tolerance_early INT UNSIGNED DEFAULT 5 COMMENT 'Tolerancia entrada anticipada (minutos)',
    tolerance_late INT UNSIGNED DEFAULT 5 COMMENT 'Tolerancia retraso (minutos)',
    working_days JSON DEFAULT '[1,2,3,4,5]' COMMENT 'Días de trabajo (1=Lunes, 7=Domingo)',
    is_night_shift TINYINT(1) DEFAULT 0 COMMENT 'Turno nocturno (cruza medianoche)',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    INDEX idx_shifts_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: shift_assignments (Asignación de turnos)
-- =====================================================
CREATE TABLE IF NOT EXISTS shift_assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    shift_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL COMMENT 'NULL = indefinido',
    is_active TINYINT(1) DEFAULT 1,
    notes TEXT NULL,
    created_by INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_shift_assignments_user_date (user_id, start_date, end_date),
    INDEX idx_shift_assignments_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: projects (Proyectos)
-- =====================================================
CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    description TEXT NULL,
    color VARCHAR(7) DEFAULT '#10B981',
    client_name VARCHAR(255) NULL,
    budget_hours DECIMAL(10,2) NULL COMMENT 'Horas presupuestadas',
    start_date DATE NULL,
    end_date DATE NULL,
    status ENUM('pending', 'active', 'paused', 'completed', 'cancelled') DEFAULT 'active',
    is_billable TINYINT(1) DEFAULT 1,
    hourly_rate DECIMAL(10,2) NULL,
    manager_id INT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_projects_tenant (tenant_id),
    INDEX idx_projects_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: tasks (Tareas)
-- =====================================================
CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    project_id INT UNSIGNED NOT NULL,
    parent_id INT UNSIGNED NULL COMMENT 'Para subtareas',
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    estimated_hours DECIMAL(8,2) NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    due_date DATE NULL,
    assigned_to INT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES tasks(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_tasks_project (project_id),
    INDEX idx_tasks_assigned (assigned_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: time_entries (Fichajes/Registros de tiempo)
-- =====================================================
CREATE TABLE IF NOT EXISTS time_entries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    work_center_id INT UNSIGNED NULL,
    project_id INT UNSIGNED NULL,
    task_id INT UNSIGNED NULL,
    
    -- Tipo de registro
    type ENUM('clock_in', 'clock_out', 'break_start', 'break_end') NOT NULL,
    
    -- Tiempos
    recorded_at DATETIME NOT NULL COMMENT 'Hora registrada',
    adjusted_at DATETIME NULL COMMENT 'Hora ajustada manualmente',
    
    -- Geolocalización
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    accuracy INT UNSIGNED NULL COMMENT 'Precisión en metros',
    location_address VARCHAR(255) NULL COMMENT 'Dirección reverse geocoding',
    
    -- Origen y validación
    source ENUM('web', 'mobile', 'terminal', 'manual', 'api') NOT NULL DEFAULT 'web',
    device_info VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    
    -- Estado
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    rejection_reason TEXT NULL,
    
    -- Extras
    notes TEXT NULL,
    metadata JSON NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (work_center_id) REFERENCES work_centers(id) ON DELETE SET NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_time_entries_user_date (user_id, recorded_at),
    INDEX idx_time_entries_tenant_date (tenant_id, recorded_at),
    INDEX idx_time_entries_type (type),
    INDEX idx_time_entries_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: daily_summaries (Resumen diario de horas)
-- =====================================================
CREATE TABLE IF NOT EXISTS daily_summaries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    
    -- Horas
    total_hours DECIMAL(5,2) DEFAULT 0 COMMENT 'Total horas trabajadas',
    regular_hours DECIMAL(5,2) DEFAULT 0 COMMENT 'Horas regulares',
    overtime_hours DECIMAL(5,2) DEFAULT 0 COMMENT 'Horas extra',
    break_hours DECIMAL(5,2) DEFAULT 0 COMMENT 'Horas de descanso',
    expected_hours DECIMAL(5,2) DEFAULT 0 COMMENT 'Horas esperadas según turno',
    
    -- Tiempos
    first_clock_in DATETIME NULL,
    last_clock_out DATETIME NULL,
    
    -- Estado
    is_complete TINYINT(1) DEFAULT 0 COMMENT 'Jornada completa',
    has_incidents TINYINT(1) DEFAULT 0 COMMENT 'Tiene incidencias',
    
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_date (tenant_id, user_id, date),
    INDEX idx_daily_summaries_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: absence_types (Tipos de ausencia)
-- =====================================================
CREATE TABLE IF NOT EXISTS absence_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NULL COMMENT 'NULL = tipo global del sistema',
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NULL,
    color VARCHAR(7) DEFAULT '#EF4444',
    icon VARCHAR(50) NULL,
    is_paid TINYINT(1) DEFAULT 1 COMMENT 'Ausencia remunerada',
    requires_approval TINYINT(1) DEFAULT 1,
    requires_document TINYINT(1) DEFAULT 0,
    max_days_per_year INT UNSIGNED NULL COMMENT 'Máximo días permitidos al año',
    advance_notice_days INT UNSIGNED DEFAULT 0 COMMENT 'Días de antelación requeridos',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    INDEX idx_absence_types_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: absences (Ausencias)
-- =====================================================
CREATE TABLE IF NOT EXISTS absences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    absence_type_id INT UNSIGNED NOT NULL,
    
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NULL COMMENT 'Para ausencias parciales',
    end_time TIME NULL,
    total_days DECIMAL(5,2) NOT NULL COMMENT 'Total días (puede ser decimal)',
    total_hours DECIMAL(6,2) NULL,
    
    reason TEXT NULL,
    document_path VARCHAR(255) NULL,
    
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    rejection_reason TEXT NULL,
    
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (absence_type_id) REFERENCES absence_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_absences_user (user_id),
    INDEX idx_absences_dates (start_date, end_date),
    INDEX idx_absences_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: holidays (Balance de vacaciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS holidays (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    year YEAR NOT NULL,
    
    entitled_days DECIMAL(5,2) NOT NULL COMMENT 'Días correspondientes',
    used_days DECIMAL(5,2) DEFAULT 0 COMMENT 'Días utilizados',
    pending_days DECIMAL(5,2) DEFAULT 0 COMMENT 'Días pendientes de aprobación',
    carried_over DECIMAL(5,2) DEFAULT 0 COMMENT 'Días del año anterior',
    extra_days DECIMAL(5,2) DEFAULT 0 COMMENT 'Días extra concedidos',
    
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_year (tenant_id, user_id, year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: calendar_events (Festivos y eventos)
-- =====================================================
CREATE TABLE IF NOT EXISTS calendar_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NULL COMMENT 'NULL = festivo nacional',
    work_center_id INT UNSIGNED NULL COMMENT 'Festivo específico de centro',
    
    name VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    type ENUM('holiday', 'company_event', 'reminder') DEFAULT 'holiday',
    is_working_day TINYINT(1) DEFAULT 0 COMMENT 'Es día laborable (evento, no festivo)',
    color VARCHAR(7) DEFAULT '#F59E0B',
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (work_center_id) REFERENCES work_centers(id) ON DELETE CASCADE,
    
    INDEX idx_calendar_events_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: audit_logs (Registro de auditoría)
-- =====================================================
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NULL,
    user_id INT UNSIGNED NULL,
    
    action VARCHAR(50) NOT NULL COMMENT 'create, update, delete, login, logout, etc.',
    entity_type VARCHAR(100) NOT NULL COMMENT 'Nombre de la tabla/entidad',
    entity_id INT UNSIGNED NULL,
    
    old_values JSON NULL,
    new_values JSON NULL,
    
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_audit_logs_tenant (tenant_id),
    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_entity (entity_type, entity_id),
    INDEX idx_audit_logs_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: notifications (Notificaciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    
    type VARCHAR(50) NOT NULL COMMENT 'absence_request, approval, reminder, etc.',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL COMMENT 'Datos adicionales',
    action_url VARCHAR(255) NULL,
    
    is_read TINYINT(1) DEFAULT 0,
    read_at DATETIME NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_notifications_user (user_id, is_read),
    INDEX idx_notifications_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: settings (Configuraciones)
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT UNSIGNED NULL COMMENT 'NULL = configuración global',
    
    `key` VARCHAR(100) NOT NULL,
    value TEXT NULL,
    type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_tenant_key (tenant_id, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
