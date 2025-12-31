<?php
/**
 * Debug: Ver qué está pasando con los POST
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug POST</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a2e; color: #eee; }
        .section { background: #16213e; padding: 15px; margin: 10px 0; border-radius: 8px; }
        h2 { color: #e94560; }
        pre { background: #0f0f23; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Debug POST Request</h1>
    
    <div class="section">
        <h2>Request Method</h2>
        <pre><?= $_SERVER['REQUEST_METHOD'] ?></pre>
    </div>
    
    <div class="section">
        <h2>$_GET</h2>
        <pre><?= print_r($_GET, true) ?></pre>
    </div>
    
    <div class="section">
        <h2>$_POST</h2>
        <pre><?= print_r($_POST, true) ?></pre>
    </div>
    
    <div class="section">
        <h2>REQUEST_URI</h2>
        <pre><?= $_SERVER['REQUEST_URI'] ?></pre>
    </div>
    
    <div class="section">
        <h2>PATH_INFO</h2>
        <pre><?= $_SERVER['PATH_INFO'] ?? 'No disponible' ?></pre>
    </div>
    
    <h2>Formulario de Prueba</h2>
    <p>Prueba enviando este formulario (simula editar empleado ID 5):</p>
    
    <form method="POST" action="index.php?route=empleados/5" style="background: #16213e; padding: 20px; border-radius: 8px;">
        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="route" value="empleados/5">
        
        <p><strong>Nombre:</strong> <input type="text" name="name" value="Test User"></p>
        <p><strong>Email:</strong> <input type="text" name="email" value="test@test.com"></p>
        <p><strong>role_id:</strong> <input type="text" name="role_id" value="2"></p>
        <p><strong>Turnos seleccionados:</strong></p>
        <p>
            <label><input type="checkbox" name="shifts[]" value="1" checked> Turno 1</label><br>
            <label><input type="checkbox" name="shifts[]" value="2" checked> Turno 2</label><br>
            <label><input type="checkbox" name="shifts[]" value="3"> Turno 3</label>
        </p>
        
        <button type="submit" style="background: #e94560; color: white; padding: 10px 20px; border: none; cursor: pointer;">
            Enviar POST
        </button>
    </form>
    
    <div class="section">
        <h2>Información adicional</h2>
        <pre>
Content-Type: <?= $_SERVER['CONTENT_TYPE'] ?? 'No definido' ?>

HTTP Headers relevantes:
<?php
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0 || strpos($key, 'CONTENT_') === 0) {
        echo "$key: $value\n";
    }
}
?>
        </pre>
    </div>
</body>
</html>
