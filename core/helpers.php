<?php
/**
 * Funciones Helper Globales
 */

use Core\Session;

if (!function_exists('old')) {
    /**
     * Obtener valor antiguo del formulario
     */
    function old(string $key, $default = null)
    {
        return Session::old($key, $default);
    }
}

if (!function_exists('session')) {
    /**
     * Obtener valor de sesión
     */
    function session(string $key = null, $default = null)
    {
        if ($key === null) {
            return $_SESSION;
        }
        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Obtener token CSRF
     */
    function csrf_token(): string
    {
        return $_SESSION['csrf_token'] ?? '';
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generar campo oculto de CSRF
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('method_field')) {
    /**
     * Generar campo oculto de método
     */
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('asset')) {
    /**
     * Generar URL de asset
     */
    function asset(string $path): string
    {
        $baseUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/');
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return $baseUrl . $basePath . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Generar URL compatible con cualquier servidor
     * Usa query string como fallback universal
     */
    function url(string $path = ''): string
    {
        $baseUrl = rtrim(($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), '/');
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        
        // Detectar si mod_rewrite está disponible
        $useModRewrite = isModRewriteEnabled();
        
        if ($useModRewrite) {
            // Con mod_rewrite: URLs limpias
            return $baseUrl . $basePath . '/' . ltrim($path, '/');
        } else {
            // Sin mod_rewrite: query string (100% compatible)
            if (empty($path) || $path === '/') {
                return $baseUrl . $basePath . '/index.php';
            }
            return $baseUrl . $basePath . '/index.php?route=' . ltrim($path, '/');
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * Redireccionar
     */
    function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Volver atrás
     */
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     */
    function dd(...$vars): void
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

if (!function_exists('dump')) {
    /**
     * Dump
     */
    function dump(...$vars): void
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML
     */
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('config')) {
    /**
     * Obtener valor de configuración
     */
    function config(string $key, $default = null)
    {
        static $config = [];
        
        if (empty($config)) {
            $configFiles = glob(dirname(__DIR__) . '/config/*.php');
            foreach ($configFiles as $file) {
                $name = basename($file, '.php');
                $config[$name] = require $file;
            }
        }
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('env')) {
    /**
     * Obtener variable de entorno
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        // Convertir valores especiales
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
}

if (!function_exists('isModRewriteEnabled')) {
    /**
     * Detectar si mod_rewrite está disponible
     */
    function isModRewriteEnabled(): bool
    {
        static $enabled = null;
        
        if ($enabled !== null) {
            return $enabled;
        }
        
        // Verificar configuración manual
        $config = config('app.use_mod_rewrite', 'auto');
        
        if ($config === true) {
            $enabled = true;
            return true;
        }
        
        if ($config === false) {
            $enabled = false;
            return false;
        }
        
        // Autodetección
        // Método 1: Verificar si apache_get_modules existe
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $enabled = in_array('mod_rewrite', $modules);
            return $enabled;
        }
        
        // Método 2: Verificar variable de entorno
        if (getenv('HTTP_MOD_REWRITE') == 'On') {
            $enabled = true;
            return true;
        }
        
        // Método 3: Verificar si estamos accediendo sin index.php
        // Si la URL no contiene index.php y estamos aquí, probablemente mod_rewrite funciona
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        if (strpos($scriptName, 'index.php') !== false && strpos($requestUri, 'index.php') === false) {
            // Accedimos sin index.php en la URL, mod_rewrite funciona
            $enabled = true;
            return true;
        }
        
        // Por defecto, asumir que NO está disponible para usar URLs seguras
        $enabled = false;
        return false;
    }
}

if (!function_exists('__')) {
    /**
     * Obtener una traducción
     * Alias corto para Translator::get()
     * 
     * Uso: __('common.save') o __('dashboard.welcome', [':name' => 'Juan'])
     */
    function __(string $key, array $replace = []): string
    {
        return \Core\Translator::get($key, $replace);
    }
}

if (!function_exists('trans')) {
    /**
     * Obtener una traducción
     * Alias largo para Translator::get()
     * 
     * Uso: trans('common.save') o trans('dashboard.welcome', [':name' => 'Juan'])
     */
    function trans(string $key, array $replace = []): string
    {
        return \Core\Translator::get($key, $replace);
    }
}

if (!function_exists('locale')) {
    /**
     * Obtener o establecer el idioma actual
     */
    function locale($language = null): string
    {
        if ($language !== null) {
            \Core\Translator::setLanguage($language);
        }
        
        return \Core\Translator::getLanguage();
    }
}

if (!function_exists('get_supported_languages')) {
    /**
     * Obtener los idiomas soportados
     * 
     * @return array Arreglo asociativo de idiomas [code => name]
     */
    function get_supported_languages(): array
    {
        return config('language.supported', []);
    }
}
