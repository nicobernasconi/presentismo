<?php
/**
 * Rutas API REST para App Móvil
 */

/** @var \Core\Router $router */

// =====================================================
// API v1
// =====================================================

$router->group(['prefix' => '/api/v1'], function ($router) {
    
    // Autenticación (públicas)
    $router->post('/auth/login', 'Api\\AuthController@login');
    $router->post('/auth/refresh', 'Api\\AuthController@refresh');
    
    // Rutas protegidas con token
    $router->group(['middleware' => ['ApiAuthMiddleware']], function ($router) {
        
        // Auth
        $router->post('/auth/logout', 'Api\\AuthController@logout');
        $router->get('/auth/me', 'Api\\AuthController@me');
        
        // Fichajes
        $router->get('/time-entries/status', 'Api\\TimeEntryController@status');
        $router->get('/time-entries/history', 'Api\\TimeEntryController@history');
        $router->get('/time-entries/summary', 'Api\\TimeEntryController@summary');
        $router->post('/time-entries/clock-in', 'Api\\TimeEntryController@clockIn');
        $router->post('/time-entries/clock-out', 'Api\\TimeEntryController@clockOut');
        $router->post('/time-entries/qr', 'Api\\TimeEntryController@qr');
        
        // Ausencias
        $router->get('/absences', 'Api\\AbsenceController@index');
        $router->get('/absences/types', 'Api\\AbsenceController@types');
        $router->post('/absences', 'Api\\AbsenceController@store');
        $router->get('/absences/{id}', 'Api\\AbsenceController@show');
        $router->delete('/absences/{id}', 'Api\\AbsenceController@cancel');
        
        // Vacaciones
        $router->get('/holidays/balance', 'Api\\HolidayController@balance');
        
        // Proyectos y tareas
        $router->get('/projects', 'Api\\ProjectController@index');
        $router->get('/projects/{id}/tasks', 'Api\\ProjectController@tasks');
        
        // Perfil
        $router->get('/profile', 'Api\\ProfileController@show');
        $router->put('/profile', 'Api\\ProfileController@update');
        $router->put('/profile/password', 'Api\\ProfileController@updatePassword');
        
        // Notificaciones
        $router->get('/notifications', 'Api\\NotificationController@index');
        $router->put('/notifications/{id}/read', 'Api\\NotificationController@markAsRead');
    });
});
