<?php
/**
 * Configuración general de la aplicación
 */

return [
    'name' => 'Sistema de Presentismo',
    'version' => '1.0.0',
    'url' => '/presentismo/public',
    'base_path' => '/presentismo/public',
    'use_mod_rewrite' => false, // false = query string (compatible con cualquier servidor)
    'debug' => true,
    'timezone' => 'Europe/Madrid',
    
    // Configuración de sesión
    'session' => [
        'lifetime' => 120, // minutos
        'name' => 'presentismo_session',
    ],
    
    // Configuración de paginación
    'pagination' => [
        'per_page' => 15,
    ],
    
    // Roles del sistema
    'roles' => [
        'super_admin' => 1,
        'admin' => 2,
        'supervisor' => 3,
        'employee' => 4,
    ],
];
