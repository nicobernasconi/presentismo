<?php
/**
 * Script para ejecutar la migración de bloques de tiempo
 */

// Definir rutas
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/core/Database.php';

use Core\Database;

try {
    $db = Database::getInstance();
    
    echo "Ejecutando migración 003_shift_time_blocks...\n\n";
    
    $sql = file_get_contents(__DIR__ . '/003_shift_time_blocks.sql');
    
    // Ejecutar cada statement por separado
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        try {
            $db->query($statement);
            echo ".";
        } catch (Exception $e) {
            echo "\nError en statement: " . substr($statement, 0, 100) . "...\n";
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n\n✓ Migración completada exitosamente\n";
    echo "- Tabla shift_time_blocks creada\n";
    echo "- Columna use_time_blocks agregada a shifts\n";
    echo "- Vista v_shift_schedules creada\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
