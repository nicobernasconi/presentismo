<?php
/**
 * Script para agregar la columna use_time_blocks si no existe
 */

define('ROOT_PATH', dirname(dirname(__DIR__)));
define('CONFIG_PATH', ROOT_PATH . '/config');

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/core/Database.php';

use Core\Database;

try {
    $db = Database::getInstance();
    
    echo "Verificando estructura de tabla shifts...\n\n";
    
    // Verificar si la columna existe
    $result = $db->fetch("SHOW COLUMNS FROM shifts LIKE 'use_time_blocks'");
    
    if (!$result) {
        echo "Columna 'use_time_blocks' no encontrada. Agregando...\n";
        
        $db->query("ALTER TABLE shifts ADD COLUMN use_time_blocks TINYINT(1) DEFAULT 0 COMMENT 'Si es 1 usa shift_time_blocks si es 0 usa start_time/end_time tradicional' AFTER is_active");
        
        echo "✓ Columna agregada exitosamente\n";
    } else {
        echo "✓ Columna 'use_time_blocks' ya existe\n";
    }
    
    // Verificar si la tabla shift_time_blocks existe
    $result = $db->fetch("SHOW TABLES LIKE 'shift_time_blocks'");
    
    if (!$result) {
        echo "\nTabla 'shift_time_blocks' no encontrada. Creando...\n";
        
        $sql = "CREATE TABLE IF NOT EXISTS shift_time_blocks (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            shift_id INT UNSIGNED NOT NULL,
            day_of_week TINYINT UNSIGNED NOT NULL COMMENT '1=Lunes, 7=Domingo',
            start_time TIME NOT NULL COMMENT 'Hora de inicio del bloque',
            end_time TIME NOT NULL COMMENT 'Hora de fin del bloque',
            block_type ENUM('work', 'break') DEFAULT 'work' COMMENT 'Tipo de bloque: trabajo o descanso',
            order_index TINYINT UNSIGNED DEFAULT 0 COMMENT 'Orden de los bloques en el día',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,
            INDEX idx_shift_day (shift_id, day_of_week),
            INDEX idx_day_time (day_of_week, start_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        COMMENT='Bloques de tiempo por día para cada turno'";
        
        $db->query($sql);
        
        echo "✓ Tabla creada exitosamente\n";
    } else {
        echo "✓ Tabla 'shift_time_blocks' ya existe\n";
    }
    
    echo "\n✅ Migración completada exitosamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
