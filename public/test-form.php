<?php
// Test para ver qué datos llegan al servidor
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Datos recibidos por POST:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Datos GET:</h2>";
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";
    
    echo "<h2>REQUEST_URI:</h2>";
    echo $_SERVER['REQUEST_URI'];
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Test Form</title></head>
<body>
    <h1>Test de Formulario</h1>
    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="_token" value="test123">
        <input type="hidden" name="route" value="empleados/2">
        
        <label>
            <input type="checkbox" name="shifts[]" value="1"> Turno 1
        </label><br>
        <label>
            <input type="checkbox" name="shifts[]" value="2"> Turno 2
        </label><br>
        <label>
            <input type="checkbox" name="shifts[]" value="3"> Turno 3
        </label><br>
        
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
