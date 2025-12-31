<?php
/**
 * Sistema de Presentismo - Front Controller
 * Punto de entrada único de la aplicación
 * 
 * URLs: index.php?route=login, index.php?route=dashboard, etc.
 */

// Definir constantes base
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', __DIR__);

// Autoloader con mapeo correcto de namespaces (case-sensitive para Linux)
spl_autoload_register(function ($class) {
    $baseDir = BASE_PATH . '/';
    
    // Mapeo de namespaces a directorios reales
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

// Cargar funciones helper
require_once CORE_PATH . '/helpers.php';

// Cargar configuración
$config = require CONFIG_PATH . '/app.php';

// Configuración de errores según entorno - SIEMPRE mostrar errores temporalmente
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Manejador de errores fatales
register_shutdown_function(function() use ($config) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<h1>Error Fatal</h1>";
        echo "<pre>";
        echo "Tipo: " . $error['type'] . "\n";
        echo "Mensaje: " . $error['message'] . "\n";
        echo "Archivo: " . $error['file'] . "\n";
        echo "Línea: " . $error['line'] . "\n";
        echo "</pre>";
    }
});

// Iniciar sesión
session_start();

// Configurar timezone
date_default_timezone_set($config['timezone']);

// Cargar el Router y procesar la solicitud
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

// DEBUG: Mostrar idioma en sesión antes de inicializar
// echo "<!-- SESSION LANGUAGE BEFORE INIT: " . ($_SESSION['language'] ?? 'NONE') . " -->\n";

// Inicializar Translator (se reinicializa cada página)
Translator::init();

// DEBUG: Mostrar idioma en Translator después de inicializar
// echo "<!-- TRANSLATOR LANGUAGE: " . Translator::getLanguage() . " -->\n";

// Inicializar conexión a base de datos con manejo de errores
try {
    Database::getInstance();
} catch (Exception $e) {
    if ($config['debug']) {
        die("Error de conexión a base de datos: " . $e->getMessage());
    }
    // En producción, continuar sin DB (puede ser que algunas rutas no la necesiten)
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
