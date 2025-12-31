<?php
namespace App\Middleware;

use App\Models\User;
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

        // Validar token JWT
        $userId = $this->validateJwtToken($token);

        if (!$userId) {
            $this->unauthorized('Token inválido o expirado');
            return false;
        }

        // Cargar usuario
        $user = User::find($userId);

        if (!$user || !$user->is_active) {
            $this->unauthorized('Usuario no encontrado o inactivo');
            return false;
        }

        // Establecer usuario en sesión para las consultas
        $_SESSION['user_id'] = $user->id;
        $_SESSION['tenant_id'] = $user->tenant_id;
        $_SESSION['api_user'] = $user;
        $_SESSION['api_token'] = $token;

        return true;
    }
    
    /**
     * Valida un token JWT
     */
    private function validateJwtToken(string $token): ?int
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        
        [$header, $payload, $signature] = $parts;
        
        $secret = $_ENV['API_SECRET'] ?? 'presentismo_api_secret_key_2024_change_this_in_production';
        $expectedSignature = hash_hmac('sha256', "$header.$payload", $secret);
        
        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }
        
        // Decodificar payload
        $data = json_decode(base64_decode($payload), true);
        
        if (!$data || !isset($data['user_id']) || !isset($data['exp'])) {
            return null;
        }
        
        // Verificar expiración
        if ($data['exp'] < time()) {
            return null;
        }
        
        // Verificar que el token existe en BD
        $db = Database::getInstance();
        $tokenHash = hash('sha256', $token);
        
        try {
            $record = $db->fetch(
                "SELECT * FROM api_tokens WHERE token = ? AND expires_at > NOW()",
                [$tokenHash]
            );
            
            if (!$record) {
                return null;
            }
            
            // Actualizar last_used_at
            $db->query(
                "UPDATE api_tokens SET last_used_at = NOW() WHERE id = ?",
                [$record['id']]
            );
        } catch (\Exception $e) {
            // Si la tabla no existe aún, validar solo por JWT
            // La tabla se creará en el primer login
        }
        
        return $data['user_id'];
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
