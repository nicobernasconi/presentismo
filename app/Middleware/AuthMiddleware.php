<?php
namespace App\Middleware;

use Core\Auth;

/**
 * Middleware de autenticación para rutas web
 */
class AuthMiddleware
{
    /**
     * Maneja la solicitud
     */
    public function handle(): bool
    {
        if (!Auth::check()) {
            // Guardar URL destino para redirección después del login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            // Redirigir al login usando URL compatible (query string si aplica)
            header('Location: ' . (function_exists('url') ? url('/login') : ((require CONFIG_PATH . '/app.php')['url'] . '/login')));
            exit;
        }

        // Verificar que el usuario sigue activo
        $user = Auth::user();
        if (!$user || !isset($user['id'])) {
            Auth::logout();
            header('Location: ' . (function_exists('url') ? url('/login') : ((require CONFIG_PATH . '/app.php')['url'] . '/login')));
            exit;
        }

        return true;
    }
}
