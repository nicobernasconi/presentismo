<?php
/**
 * Configuración de base de datos
 */

return [
    'driver' => 'mysql',
    'host' => '190.228.29.53',
    'port' => 3306,
    'database' => 'presentismo',
    'username' => 'presentismo',
    'password' => 'MundoPresentismo2025',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
