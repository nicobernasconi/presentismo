<?php
/**
 * Test para verificar el flujo completo de cambio de idioma
 */

session_start();

echo "<h1>Test de Cambio de Idioma</h1>";

// Mostrar sesión actual
echo "<h2>Estado actual:</h2>";
echo "<pre>";
echo "Idioma en sesión: " . ($_SESSION['language'] ?? 'NO DEFINIDO') . "\n";
echo "</pre>";

echo "<h2>Simular POST a /cambiar-idioma:</h2>";

// Simular un POST request
$_POST['language'] = 'en';
$_POST['csrf_token'] = $_SESSION['csrf_token'] ?? 'TEST_TOKEN';

echo "<pre>";
echo "POST language: " . $_POST['language'] . "\n";
echo "POST csrf_token: " . $_POST['csrf_token'] . "\n";
echo "</pre>";

// Cambiar idioma
$_SESSION['language'] = $_POST['language'];

echo "<h2>Después del cambio:</h2>";
echo "<pre>";
echo "Idioma en sesión: " . $_SESSION['language'] . "\n";
echo "</pre>";

echo "<h2>Links para probar en el dashboard real:</h2>";
echo "<ul>";
echo '<li><a href="' . $_SERVER['HTTP_REFERER'] . '">Volver al dashboard</a></li>';
echo '<li><a href="/presentismo/public/index.php?route=dashboard">Ir al dashboard</a></li>';
echo '</ul>';

echo "<h2>Verificar si funcionó:</h2>";
echo "<p>Haz clic en 'Volver al dashboard' y verifica si el idioma cambió a inglés.</p>";
echo "<p>Si no cambia, el problema está en cómo se renderiza la página después de la redirección.</p>";
