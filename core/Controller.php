<?php
namespace Core;

/**
 * Controller - Clase base para controladores
 */
abstract class Controller
{
    protected array $data = [];
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Renderiza una vista
     */
    protected function view(string $view, array $data = []): void
    {
        $this->data = array_merge($this->data, $data);

        // Asegurar que $baseUrl y $assetsUrl estén disponibles en las vistas
        // Genera la base URL correcta según el modo (mod_rewrite o query string)
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        
        // $assetsUrl siempre apunta a la carpeta public para CSS, JS, imágenes
        $this->data['assetsUrl'] = $scriptDir;
        
        if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
            // Sin mod_rewrite: usar index.php con query string para rutas
            $this->data['baseUrl'] = $scriptDir . '/index.php?route=';
        } else {
            // Con mod_rewrite o fallback: URL base limpia
            try {
                $config = require CONFIG_PATH . '/app.php';
                $this->data['baseUrl'] = rtrim($config['url'] ?? '', '/');
            } catch (\Throwable $e) {
                $this->data['baseUrl'] = $scriptDir ?: '';
            }
        }

        $viewPath = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("Vista no encontrada: {$view}");
        }

        // Buffer de salida para capturar el contenido
        ob_start();
        extract($this->data);
        require $viewPath;
        $content = ob_get_clean();

        // Si hay layout, incluirlo
        if (isset($this->data['layout'])) {
            // Soportar rutas completas como 'admin.layouts.app' o 'layouts.app'
            $layout = $this->data['layout'];

            // Normalizar alias comunes sin ruta a layouts/*
            // 'app' -> 'layouts/app', 'guest' -> 'layouts/guest'
            if (strpos($layout, '/') === false && strpos($layout, '.') === false) {
                if ($layout === 'app') {
                    $layout = 'layouts/app';
                } elseif ($layout === 'guest') {
                    $layout = 'layouts/guest';
                }
            }

            // Si contiene punto, reemplazar con barras (admin.layouts.app -> admin/layouts/app)
            if (strpos($layout, '.') !== false) {
                $layout = str_replace('.', '/', $layout);
            }

            $layoutPath = APP_PATH . '/Views/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                // Si 'user' no fue explícitamente pasada, cargar el usuario autenticado
                if (!array_key_exists('user', $this->data)) {
                    $this->data['user'] = Auth::user() ?? [];
                }
                $layoutData = array_merge($this->data, ['content' => $content, 'viewPath' => $viewPath]);
                extract($layoutData);
                require $layoutPath;
                return;
            }
        }

        echo $content;
    }

    /**
     * Establece el layout a usar
     */
    protected function setLayout(string $layout): void
    {
        $this->data['layout'] = $layout;
    }

    /**
     * Respuesta JSON
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirección
     */
    protected function redirect(string $url): void
    {
        // Construir la URL base correcta
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        
        if (function_exists('isModRewriteEnabled') && !isModRewriteEnabled()) {
            // Sin mod_rewrite: usar query string
            $baseUrl = $scriptDir . '/index.php?route=';
        } else {
            // Con mod_rewrite
            $config = require CONFIG_PATH . '/app.php';
            $baseUrl = rtrim($config['url'] ?? $scriptDir, '/');
        }
        
        if (strpos($url, 'http') !== 0) {
            // Quitar barra inicial si existe para evitar doble barra
            $url = ltrim($url, '/');
            $url = $baseUrl . $url;
        }
        
        header("Location: {$url}");
        exit;
    }

    /**
     * Obtiene datos de la solicitud POST
     */
    protected function input(string $key = null, $default = null)
    {
        $data = $_POST;
        
        // Si es JSON, decodificar
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    /**
     * Obtiene parámetros GET
     */
    protected function query(string $key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Valida datos según reglas
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value) && $value !== '0') {
                            $errors[$field][] = "El campo {$field} es requerido.";
                        }
                        break;

                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "El campo {$field} debe ser un email válido.";
                        }
                        break;

                    case 'min':
                        if ($value && strlen($value) < (int) $ruleParam) {
                            $errors[$field][] = "El campo {$field} debe tener al menos {$ruleParam} caracteres.";
                        }
                        break;

                    case 'max':
                        if ($value && strlen($value) > (int) $ruleParam) {
                            $errors[$field][] = "El campo {$field} no debe exceder {$ruleParam} caracteres.";
                        }
                        break;

                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = "El campo {$field} debe ser numérico.";
                        }
                        break;

                    case 'date':
                        if ($value && !strtotime($value)) {
                            $errors[$field][] = "El campo {$field} debe ser una fecha válida.";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Almacena errores en sesión (flash)
     */
    protected function withErrors(array $errors): self
    {
        $_SESSION['errors'] = $errors;
        return $this;
    }

    /**
     * Almacena datos antiguos en sesión
     */
    protected function withOld(array $data): self
    {
        $_SESSION['old'] = $data;
        return $this;
    }

    /**
     * Almacena mensaje de éxito
     */
    protected function withSuccess(string $message): self
    {
        $_SESSION['success'] = $message;
        return $this;
    }

    /**
     * Almacena mensaje de error
     */
    protected function withError(string $message): self
    {
        $_SESSION['error'] = $message;
        return $this;
    }

    /**
     * Almacena mensaje de advertencia
     */
    protected function withWarning(string $message): self
    {
        $_SESSION['warning'] = $message;
        return $this;
    }

    /**
     * Valida el token CSRF
     */
    protected function validateCsrfToken(): void
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($token) || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            die('Token CSRF inválido');
        }
    }

    /**
     * Establece un mensaje flash en sesión
     */
    protected function setFlashMessage(string $type, string $message): void
    {
        $_SESSION[$type] = $message;
    }
}
