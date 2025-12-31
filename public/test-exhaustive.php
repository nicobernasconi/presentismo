<?php
/**
 * Test exhaustivo del cambio de idioma
 */

// Paso 1: Verificar sesión actual
session_start();

echo "<h1>Test Exhaustivo de Cambio de Idioma</h1>";

echo "<h2>1. Estado Inicial:</h2>";
echo "<pre>";
echo "Idioma en sesión: " . ($_SESSION['language'] ?? "NO DEFINIDO (será 'es')") . "\n";
echo "ID de sesión: " . session_id() . "\n";
echo "</pre>";

// Paso 2: Simular cambio a 'en'
echo "<h2>2. Cambiar a English (en):</h2>";
$_SESSION['language'] = 'en';
echo "<p>✅ \$_SESSION['language'] = 'en'</p>";

// Paso 3: Verificar que se guardó
echo "<h2>3. Verificar que se guardó:</h2>";
echo "<pre>";
echo "Idioma en sesión ahora: " . $_SESSION['language'] . "\n";
echo "ID de sesión: " . session_id() . "\n";
echo "</pre>";

// Paso 4: Simular redirección
echo "<h2>4. Simular redirección a dashboard:</h2>";
echo '<p><a href="/presentismo/public/index.php?route=dashboard&test=' . time() . '">IR A DASHBOARD</a></p>';

// Paso 5: Crear un formulario para POST
echo "<h2>5. O usar formulario POST:</h2>";
echo "<form method='POST' action='/presentismo/public/index.php?route=/cambiar-idioma'>";
echo "<input type='hidden' name='language' value='ca'>";
echo "<button type='submit'>Cambiar a Catalán (ca) via POST</button>";
echo "</form>";

// Paso 6: Info
echo "<h2>6. Información:</h2>";
echo "<ul>";
echo "<li>Todos los tests de traducción funcionan correctamente (comprobado)</li>";
echo "<li>La sesión se guarda en test-language.php (comprobado)</li>";
echo "<li>Translator::init() detecta el idioma de sesión (comprobado)</li>";
echo "<li><strong>PRÓXIMO PASO:</strong> Verificar si la sesión se mantiene después de redirección</li>";
echo "</ul>";
