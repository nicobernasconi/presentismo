<?php
namespace App\Middleware;

use Core\Database;

/**
 * Middleware de autenticación para API
 */
class ApiAuthMiddleware
{
    /**
     * Maneja la solicitud
     */
    public function handle(): bool
    {
        $token = $this->getBearerToken();

        if (!$token) {
            $this->unauthorized('Token no proporcionado');
            return false;
        }

        // Verificar token en la base de datos
        $db = Database::getInstance();
        $sql = "SELECT u.* FROM users u 
                WHERE u.remember_token = ? 
                AND u.is_active = 1 
                AND u.deleted_at IS NULL";
        
        $user = $db->fetch($sql, [$token]);

        if (!$user) {
            $this->unauthorized('Token inválido o expirado');
            return false;
        }

        // Establecer usuario en sesión para las consultas
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['tenant_id'] = $user['tenant_id'];
        $_SESSION['api_user'] = $user;

        return true;
    }

    /**
     * Extrae el token Bearer del header Authorization
     */
    private function getBearerToken(): ?string
    {
        $headers = $this->getAuthorizationHeader();
        
        if (!$headers) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Obtiene el header Authorization
     */
    private function getAuthorizationHeader(): ?string
    {
        if (isset($_SERVER['Authorization'])) {
            return trim($_SERVER['Authorization']);
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                return trim($headers['Authorization']);
            }
            if (isset($headers['authorization'])) {
                return trim($headers['authorization']);
            }
        }

        return null;
    }

    /**
     * Respuesta de no autorizado
     */
    private function unauthorized(string $message): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message,
        ]);
        exit;
    }
}
