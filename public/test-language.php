<?php
/**
 * Script de prueba para verificar cambio de idioma
 */

session_start();

echo "<h1>Test de Idioma</h1>";
echo "<pre>";
echo "Idioma actual en sesión: " . ($_SESSION['language'] ?? 'no definido') . "\n";
echo "Idioma en GET: " . ($_GET['language'] ?? 'no definido') . "\n";
echo "</pre>";

if (isset($_GET['language'])) {
    $_SESSION['language'] = $_GET['language'];
    echo "<p>✅ Idioma guardado en sesión a: " . $_SESSION['language'] . "</p>";
    echo '<p><a href="test-language.php">Ver sesión guardada</a></p>';
}

echo "<h2>Enlaces de prueba:</h2>";
echo '<ul>';
echo '<li><a href="test-language.php?language=es">Cambiar a Español</a></li>';
echo '<li><a href="test-language.php?language=en">Cambiar a English</a></li>';
echo '<li><a href="test-language.php?language=ca">Cambiar a Català</a></li>';
echo '</ul>';

// Mostrar toda la sesión
echo "<h2>Contenido completo de \$_SESSION:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
