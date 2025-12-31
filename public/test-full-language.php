<?php
/**
 * Script para simular el cambio de idioma completo
 */

// Configuración idéntica a index.php
define('BASE_PATH', dirname(__DIR__));
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');

// Autoloader
spl_autoload_register(function ($class) {
    $baseDir = BASE_PATH . '/';
    $namespaceMap = [
        'App\\' => 'app/',
        'Core\\' => 'core/',
    ];
    
    foreach ($namespaceMap as $namespace => $directory) {
        $len = strlen($namespace);
        if (strncmp($namespace, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . $directory . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
            }
            return;
        }
    }
});

// Cargar helpers y config
require_once CORE_PATH . '/helpers.php';
$config = require CONFIG_PATH . '/app.php';
require_once CORE_PATH . '/Translator.php';

// Iniciar sesión
session_start();

use Core\Translator;

echo "<h1>Simulación de Cambio de Idioma</h1>";
echo "<pre>";

// Paso 1: Sesión inicial
echo "=== PASO 1: SESIÓN INICIAL ===\n";
echo "Idioma en sesión: " . ($_SESSION['language'] ?? 'NO DEFINIDO') . "\n";

// Paso 2: Cambiar el idioma
echo "\n=== PASO 2: CAMBIAR IDIOMA A 'en' ===\n";
$_SESSION['language'] = 'en';
echo "Idioma ahora en sesión: " . $_SESSION['language'] . "\n";

// Paso 3: Inicializar Translator
echo "\n=== PASO 3: INICIALIZAR TRANSLATOR ===\n";
Translator::init();
echo "Idioma actual en Translator: " . Translator::getLanguage() . "\n";

// Paso 4: Pruebas de traducción
echo "\n=== PASO 4: PRUEBAS DE TRADUCCIÓN ===\n";
echo "common.save (debería ser 'Save'): " . Translator::get('common.save') . "\n";
echo "auth.login_title (debería ser 'Login'): " . Translator::get('auth.login_title') . "\n";
echo "dashboard.title (debería ser 'Dashboard'): " . Translator::get('dashboard.title') . "\n";

// Paso 5: Cambiar a Catalan
echo "\n=== PASO 5: CAMBIAR A CATALÁN ===\n";
$_SESSION['language'] = 'ca';
Translator::init();
echo "Idioma ahora en Translator: " . Translator::getLanguage() . "\n";
echo "common.save (debería ser 'Guardar'): " . Translator::get('common.save') . "\n";

echo "\n=== CONCLUSIÓN ===\n";
echo "Si las traducciones son correctas, el problema está en la redirección\n";
echo "Si las traducciones son incorrectas, hay un problema con los archivos\n";

echo "</pre>";
?>

<h2>Archivos de traducción disponibles:</h2>
<pre>
<?php
$langPath = BASE_PATH . '/resources/lang';
$dirs = array_filter(glob($langPath . '/*'), 'is_dir');
foreach ($dirs as $dir) {
    $lang = basename($dir);
    echo "\n$lang/\n";
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        echo "  - " . basename($file) . "\n";
    }
}
?>
</pre>
