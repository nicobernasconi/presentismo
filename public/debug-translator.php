<?php
/**
 * Script para debug del Translator
 */

// Configuración idéntica a index.php
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
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

// Iniciar sesión
session_start();

// Cargar Translator
require_once CORE_PATH . '/Translator.php';

use Core\Translator;

echo "<h1>Debug Translator</h1>";
echo "<pre>";
echo "1. \$_SESSION['language']: " . ($_SESSION['language'] ?? 'NO DEFINIDO') . "\n";
echo "2. Contenido de \$_SESSION:\n";
print_r($_SESSION);
echo "\n3. Config de idiomas:\n";
print_r($config['supported'] ?? 'NO DISPONIBLE');
echo "\n4. Inicializando Translator...\n";

Translator::init();

echo "5. Idioma actual del Translator: " . Translator::getLanguage() . "\n";
echo "6. Prueba de traducción (common.save): " . Translator::get('common.save') . "\n";
echo "7. Prueba de traducción (auth.login_title): " . Translator::get('auth.login_title') . "\n";
echo "</pre>";
