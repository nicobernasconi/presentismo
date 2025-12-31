<?php
namespace App\Middleware;

use Core\Auth;

/**
 * Middleware para verificar rol de administrador
 */
class AdminMiddleware
{
    /**
     * Maneja la solicitud
     */
    public function handle(): bool
    {
        if (!Auth::check()) {
            header('Location: ' . (require CONFIG_PATH . '/app.php')['url'] . '/login');
            exit;
        }

        if (!Auth::isAdmin()) {
            http_response_code(403);
            require_once APP_PATH . '/Views/errors/403.php';
            exit;
        }

        return true;
    }
}
