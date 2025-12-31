<?php
/**
 * Rutas Web del Panel de Administración
 */

/** @var \Core\Router $router */

// =====================================================
// Rutas de Administración Super Admin
// =====================================================

$router->get('/admin/login', 'AdminAuthController@login');
$router->post('/admin/login', 'AdminAuthController@authenticate');

$router->group(['prefix' => '/admin', 'middleware' => ['AdminAuthMiddleware']], function ($router) {
    $router->get('/dashboard', 'AdminDashboardController@index');
    $router->get('/logout', 'AdminAuthController@logout');
    
    // Gestión de Empresas
    $router->get('/empresas', 'AdminCompaniesController@index');
    $router->get('/empresas/crear', 'AdminCompaniesController@create');
    $router->post('/empresas', 'AdminCompaniesController@store');
    $router->get('/empresas/{id}', 'AdminCompaniesController@show');
    $router->get('/empresas/{id}/editar', 'AdminCompaniesController@edit');
    $router->post('/empresas/{id}', 'AdminCompaniesController@update');
    $router->post('/empresas/{id}/eliminar', 'AdminCompaniesController@destroy');
    
    // Gestión de Planes
    $router->get('/planes', 'AdminPlansController@index');
    $router->get('/planes/crear', 'AdminPlansController@create');
    $router->post('/planes', 'AdminPlansController@store');
    $router->get('/planes/{id}/editar', 'AdminPlansController@edit');
    $router->post('/planes/{id}', 'AdminPlansController@update');
    $router->post('/planes/{id}/eliminar', 'AdminPlansController@destroy');
    
    // Gestión de Usuarios (Superadmins por empresa)
    $router->get('/usuarios', 'AdminUsersController@index');
    $router->get('/usuarios/crear', 'AdminUsersController@create');
    $router->post('/usuarios', 'AdminUsersController@store');
    $router->get('/usuarios/{id}/editar', 'AdminUsersController@edit');
    $router->post('/usuarios/{id}', 'AdminUsersController@update');
    $router->post('/usuarios/{id}/eliminar', 'AdminUsersController@destroy');
});

// =====================================================
// Rutas Públicas (sin autenticación)
// =====================================================

$router->get('/', 'AuthController@showLogin');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegisterCompany');
$router->post('/register', 'AuthController@registerCompany');
$router->get('/register-company', 'AuthController@showRegisterCompany');
$router->post('/register-company', 'AuthController@registerCompany');
$router->get('/logout', 'AuthController@logout');

// Cambiar idioma
$router->get('/cambiar-idioma', 'LanguageController@change');
$router->post('/cambiar-idioma', 'LanguageController@change');

// Páginas legales
$router->get('/privacidad', 'LegalController@privacy');
$router->get('/cookies', 'LegalController@cookies');
$router->get('/terminos', 'LegalController@terms');

// =====================================================
// Rutas Públicas de Fichaje por QR
// =====================================================
$router->get('/fichar/{token}', 'QrController@scan');
$router->get('/fichar/{token}/{workCenterId}', 'QrController@scan');
$router->post('/fichar/accion', 'QrController@clockAction');

// =====================================================
// Rutas Protegidas (requieren autenticación)
// =====================================================

// Ruta protegida para /fichajes (aplica AuthMiddleware)
$router->get('/fichajes', 'TimeEntryController@index', ['AuthMiddleware']);

// Alias directo sin middleware como fallback (index hace guard)
$router->get('/fichajes', 'TimeEntryController@index');

$router->group(['prefix' => '', 'middleware' => ['AuthMiddleware']], function ($router) {
    
    // Dashboard
    $router->get('/dashboard', 'DashboardController@index');
    
    // QR Management (solo admin)
    $router->get('/qr', 'QrController@index', ['AdminMiddleware']);
    $router->get('/qr/generar', 'QrController@generate', ['AdminMiddleware']);
    
    // Fichajes
    // Nota: también registrada arriba para asegurar coincidencia directa
    // $router->get('/fichajes', 'TimeEntryController@index');
    $router->get('/fichajes/escanear', 'TimeEntryController@showScanQr');
    $router->get('/fichajes/lista', 'TimeEntryController@listAll');
    $router->post('/fichajes/scan-qr', 'TimeEntryController@processScanQr');
    $router->post('/fichajes/test-qr', 'TimeEntryController@testQr');
    $router->post('/fichajes/location-clock', 'TimeEntryController@processLocationClock');
    // Permitir acceso por GET para evitar 404 al abrir URLs directamente
    $router->get('/fichajes/clock-in', 'TimeEntryController@clockIn');
    $router->post('/fichajes/clock-in', 'TimeEntryController@clockIn');
    $router->get('/fichajes/clock-out', 'TimeEntryController@clockOut');
    $router->post('/fichajes/clock-out', 'TimeEntryController@clockOut');
    $router->get('/fichajes/historial', 'TimeEntryController@history');
    $router->get('/fichajes/{id}', 'TimeEntryController@show');
    $router->post('/fichajes/{id}/aprobar', 'TimeEntryController@approve');
    $router->post('/fichajes/{id}/rechazar', 'TimeEntryController@reject');
    
    // Empleados
    $router->get('/empleados', 'EmployeeController@index');
    $router->get('/empleados/crear', 'EmployeeController@create');
    $router->post('/empleados', 'EmployeeController@store');
    $router->get('/empleados/{id}', 'EmployeeController@show');
    $router->get('/empleados/{id}/editar', 'EmployeeController@edit');
    $router->post('/empleados/{id}', 'EmployeeController@update');
    $router->post('/empleados/{id}/eliminar', 'EmployeeController@destroy');
    
    // Departamentos
    $router->get('/departamentos', 'DepartmentController@index');
    $router->get('/departamentos/crear', 'DepartmentController@create');
    $router->post('/departamentos', 'DepartmentController@store');
    $router->get('/departamentos/{id}/editar', 'DepartmentController@edit');
    $router->post('/departamentos/{id}', 'DepartmentController@update');
    $router->post('/departamentos/{id}/eliminar', 'DepartmentController@destroy');
    
    // Centros de Trabajo
    $router->get('/centros', 'WorkCenterController@index');
    $router->get('/centros/crear', 'WorkCenterController@create');
    $router->post('/centros', 'WorkCenterController@store');
    $router->get('/centros/{id}/editar', 'WorkCenterController@edit');
    $router->post('/centros/{id}', 'WorkCenterController@update');
    $router->post('/centros/{id}/eliminar', 'WorkCenterController@destroy');
    
    // Turnos
    $router->get('/turnos', 'ShiftController@index');
    $router->get('/turnos/crear', 'ShiftController@create');
    $router->post('/turnos', 'ShiftController@store');
    $router->get('/turnos/{id}/editar', 'ShiftController@edit');
    $router->post('/turnos/{id}', 'ShiftController@update');
    $router->get('/turnos/{id}/bloques', 'ShiftController@manageBlocks');
    $router->post('/turnos/{id}/bloques', 'ShiftController@saveBlocks');
    $router->post('/turnos/{id}/eliminar', 'ShiftController@destroy');
    
    // Asignaciones de Turnos
    $router->get('/turnos/asignaciones', 'ShiftAssignmentController@index');
    $router->get('/turnos/asignaciones/crear', 'ShiftAssignmentController@create');
    $router->post('/turnos/asignaciones/guardar', 'ShiftAssignmentController@store');
    $router->post('/turnos/asignaciones/{id}/desactivar', 'ShiftAssignmentController@deactivate');
    $router->post('/turnos/asignaciones/{id}/eliminar', 'ShiftAssignmentController@delete');
    $router->get('/turnos/asignaciones/empleado/{userId}', 'ShiftAssignmentController@employee');
    $router->get('/turnos/verificar/{userId}', 'ShiftAssignmentController@checkClock');
    
    // Ausencias
    $router->get('/ausencias', 'AbsenceController@index');
    $router->get('/ausencias/solicitar', 'AbsenceController@create');
    $router->post('/ausencias', 'AbsenceController@store');
    $router->get('/ausencias/{id}', 'AbsenceController@show');
    $router->post('/ausencias/{id}/aprobar', 'AbsenceController@approve');
    $router->post('/ausencias/{id}/rechazar', 'AbsenceController@reject');
    $router->post('/ausencias/{id}/cancelar', 'AbsenceController@cancel');
    
    // Vacaciones
    $router->get('/vacaciones', 'HolidayController@index');
    $router->get('/vacaciones/calendario', 'HolidayController@calendar');
    
    // Proyectos
    $router->get('/proyectos', 'ProjectController@index');
    $router->get('/proyectos/crear', 'ProjectController@create');
    $router->post('/proyectos', 'ProjectController@store');
    $router->get('/proyectos/{id}', 'ProjectController@show');
    $router->get('/proyectos/{id}/editar', 'ProjectController@edit');
    $router->post('/proyectos/{id}', 'ProjectController@update');
    $router->post('/proyectos/{id}/eliminar', 'ProjectController@destroy');
    
    // Tareas
    $router->get('/tareas', 'TaskController@index');
    $router->post('/tareas', 'TaskController@store');
    $router->post('/tareas/{id}', 'TaskController@update');
    $router->post('/tareas/{id}/eliminar', 'TaskController@destroy');
    
    // Reportes
    $router->get('/reportes', 'ReportController@index');
    $router->get('/reportes/fichajes', 'ReportController@timeEntries');
    $router->get('/reportes/ausencias', 'ReportController@absences');
    $router->get('/reportes/horas', 'ReportController@hours');
    $router->get('/reportes/exportar', 'ReportController@export');
    
    // Configuración
    $router->get('/configuracion', 'SettingController@index');
    $router->post('/configuracion', 'SettingController@update');
    $router->get('/configuracion/empresa', 'SettingController@company');
    $router->post('/configuracion/empresa', 'SettingController@updateCompany');
    
    // Perfil
    $router->get('/perfil', 'ProfileController@index');
    $router->post('/perfil', 'ProfileController@update');
    $router->post('/perfil/password', 'ProfileController@updatePassword');
    
    // Notificaciones
    $router->get('/notificaciones', 'NotificationController@index');
    $router->post('/notificaciones/{id}/leer', 'NotificationController@markAsRead');
    $router->post('/notificaciones/leer-todas', 'NotificationController@markAllAsRead');
});

