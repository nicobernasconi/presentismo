<?php
namespace Core;

/**
 * Router - Maneja las rutas de la aplicación
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $prefix = '';
    private array $groupMiddlewares = [];

    /**
     * Registra una ruta GET
     */
    public function get(string $path, $handler, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * Registra una ruta POST
     */
    public function post(string $path, $handler, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * Registra una ruta PUT
     */
    public function put(string $path, $handler, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    /**
     * Registra una ruta DELETE
     */
    public function delete(string $path, $handler, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    /**
     * Agrupa rutas con un prefijo y middlewares comunes
     */
    public function group(array $options, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $previousMiddlewares = $this->groupMiddlewares;

        $this->prefix .= $options['prefix'] ?? '';
        $this->groupMiddlewares = array_merge(
            $this->groupMiddlewares,
            $options['middleware'] ?? []
        );

        $callback($this);

        $this->prefix = $previousPrefix;
        $this->groupMiddlewares = $previousMiddlewares;
    }

    /**
     * Agrega una ruta al registro
     */
    private function addRoute(string $method, string $path, $handler, array $middlewares): self
    {
        $fullPath = $this->prefix . $path;
        $allMiddlewares = array_merge($this->groupMiddlewares, $middlewares);

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middlewares' => $allMiddlewares,
            'pattern' => $this->convertToPattern($fullPath),
        ];

        return $this;
    }

    /**
     * Convierte una ruta a patrón regex
     */
    private function convertToPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . rtrim($pattern, '/') . '/?$#';
    }

    /**
     * Despacha la solicitud actual
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        // Soporte para métodos PUT/DELETE via POST
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extraer parámetros
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Ejecutar middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareClass = "App\\Middleware\\{$middleware}";
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        $result = $middlewareInstance->handle();
                        if ($result === false) {
                            return;
                        }
                    }
                }

                // Ejecutar handler
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }

        // Ruta no encontrada
        $this->notFound();
    }

    /**
     * Obtiene la URI actual - Prioriza query string para máxima compatibilidad
     */
    private function getUri(): string
    {
        // Método 1: Query string (?route=xxx) - MÁS compatible con cualquier servidor
        if (isset($_GET['route']) && !empty($_GET['route'])) {
            $route = $_GET['route'];
            return '/' . ltrim($route, '/');
        }
        
        // Método 1b: POST route (para formularios sin mod_rewrite)
        if (isset($_POST['route']) && !empty($_POST['route'])) {
            $route = $_POST['route'];
            return '/' . ltrim($route, '/');
        }
        
        // Método 2: PATH_INFO (si disponible)
        if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        }
        
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Remover base path si existe
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('/index.php', '', $scriptName);
        
        if ($basePath && $basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Remover index.php si está presente en la URI
        $uri = str_replace('/index.php', '', $uri);
        $uri = preg_replace('#^/index\.php/#', '/', $uri);

        return $uri ?: '/';
    }

    /**
     * Ejecuta el handler de la ruta
     */
    private function executeHandler($handler, array $params): void
    {
        // Convertir array asociativo a array indexado (solo los valores)
        $paramValues = array_values($params);
        
        if (is_callable($handler)) {
            call_user_func_array($handler, $paramValues);
            return;
        }

        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                call_user_func_array([$controllerInstance, $method], $paramValues);
                return;
            }
        }

        $this->notFound();
    }

    /**
     * Respuesta 404
     */
    private function notFound(): void
    {
        http_response_code(404);
        require_once APP_PATH . '/Views/errors/404.php';
    }
}
