<?php
/**
 * Test directo de inserción en shift_assignments
 */

// Cargar configuración
require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';

try {
    $dsn = sprintf(
        '%s:host=%s;port=%d;dbname=%s;charset=%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );
    
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Conexión exitosa a la base de datos: {$config['database']}</h2>";
    
    // Ver datos actuales
    echo "<h3>Datos actuales en shift_assignments:</h3>";
    $stmt = $pdo->query("SELECT * FROM shift_assignments");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($rows, true) . "</pre>";
    
    // Intentar insertar
    if (isset($_GET['insert'])) {
        $sql = "INSERT INTO shift_assignments (tenant_id, user_id, shift_id, start_date, is_active, created_at, updated_at) 
                VALUES (1, 2, 2, CURDATE(), 1, NOW(), NOW())";
        $pdo->exec($sql);
        echo "<p style='color:green;font-weight:bold;'>INSERTADO CON ÉXITO!</p>";
        
        // Mostrar de nuevo
        $stmt = $pdo->query("SELECT * FROM shift_assignments");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Datos después de insertar:</h3>";
        echo "<pre>" . print_r($rows, true) . "</pre>";
    } else {
        echo "<p><a href='?insert=1'>Hacer clic aquí para probar INSERT</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
