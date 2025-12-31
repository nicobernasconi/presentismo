<?php
/**
 * Script para verificar qué está pasando en el dashboard
 */

// Igual que public/index.php pero con debug

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', __DIR__);

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

require_once CORE_PATH . '/helpers.php';
$config = require CONFIG_PATH . '/app.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

date_default_timezone_set($config['timezone']);

require_once CORE_PATH . '/Router.php';
require_once CORE_PATH . '/Database.php';
require_once CORE_PATH . '/Controller.php';
require_once CORE_PATH . '/Model.php';
require_once CORE_PATH . '/Auth.php';
require_once CORE_PATH . '/Session.php';
require_once CORE_PATH . '/Translator.php';

use Core\Router;
use Core\Database;
use Core\Translator;
use Core\Auth;

// DEBUG: Mostrar estado antes de inicializar Translator
echo "<!-- DEBUG: ANTES DE INICIALIZAR TRANSLATOR -->\n";
echo "<!-- \$_SESSION['language']: " . ($_SESSION['language'] ?? 'NO DEFINIDO') . " -->\n";

// Inicializar Translator
Translator::init();

// DEBUG: Mostrar estado después de inicializar
echo "<!-- DEBUG: DESPUÉS DE INICIALIZAR TRANSLATOR -->\n";
echo "<!-- Idioma en Translator: " . Translator::getLanguage() . " -->\n";
echo "<!-- Prueba: " . Translator::get('common.save') . " -->\n";

// Iniciar conexión a base de datos
try {
    Database::getInstance();
} catch (Exception $e) {
    if ($config['debug']) {
        die("Error de conexión a base de datos: " . $e->getMessage());
    }
}

// Cargar rutas
$router = new Router();
require_once BASE_PATH . '/routes/web.php';
require_once BASE_PATH . '/routes/api.php';

// Procesar la solicitud
try {
    $router->dispatch();
} catch (Throwable $e) {
    if ($config['debug']) {
        echo "<h1>Error en la aplicación</h1>";
        echo "<pre>";
        echo "Tipo: " . get_class($e) . "\n";
        echo "Mensaje: " . $e->getMessage() . "\n";
        echo "Archivo: " . $e->getFile() . "\n";
        echo "Línea: " . $e->getLine() . "\n\n";
        echo "Stack trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    } else {
        http_response_code(500);
        echo "Error interno del servidor";
    }
}
