<?php
namespace App\Controllers\Api;

use App\Models\User;
use Core\Database;

/**
 * Controlador de Autenticación API
 */
class AuthController extends ApiController
{
    /**
     * Login - Autenticar usuario y generar token
     * POST /api/v1/auth/login
     * 
     * Body: { "email": "...", "password": "..." }
     * Response: { "success": true, "data": { "token": "...", "user": {...} } }
     */
    public function login(): void
    {
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (!empty($errors)) {
            $this->error('Datos de acceso inválidos', 422, $errors);
        }
        
        $email = $this->input('email');
        $password = $this->input('password');
        
        // Buscar usuario
        $user = User::findByEmail($email);
        
        if (!$user || !password_verify($password, $user->password)) {
            $this->error('Credenciales incorrectas', 401);
        }
        
        if (!$user->is_active) {
            $this->error('Tu cuenta está desactivada', 403);
        }
        
        // Generar token
        $token = $this->generateToken($user);
        
        // Guardar token en BD
        $this->storeToken($user->id, $token);
        
        // Obtener datos adicionales del usuario
        $userData = $this->getUserData($user);
        
        $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 86400 * 30, // 30 días
            'user' => $userData,
        ], 'Login exitoso');
    }
    
    /**
     * Refresh token
     * POST /api/v1/auth/refresh
     */
    public function refresh(): void
    {
        $token = $this->getBearerToken();
        
        if (!$token) {
            $this->error('Token no proporcionado', 401);
        }
        
        $userId = $this->validateToken($token);
        
        if (!$userId) {
            $this->error('Token inválido o expirado', 401);
        }
        
        $user = User::find($userId);
        
        if (!$user || !$user->is_active) {
            $this->error('Usuario no encontrado o inactivo', 401);
        }
        
        // Invalidar token anterior
        $this->invalidateToken($token);
        
        // Generar nuevo token
        $newToken = $this->generateToken($user);
        $this->storeToken($user->id, $newToken);
        
        $this->success([
            'token' => $newToken,
            'token_type' => 'Bearer',
            'expires_in' => 86400 * 30,
        ], 'Token renovado');
    }
    
    /**
     * Logout - Invalidar token
     * POST /api/v1/auth/logout
     */
    public function logout(): void
    {
        $token = $this->getBearerToken();
        
        if ($token) {
            $this->invalidateToken($token);
        }
        
        $this->success([], 'Sesión cerrada correctamente');
    }
    
    /**
     * Obtener datos del usuario autenticado
     * GET /api/v1/auth/me
     */
    public function me(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        $userData = $this->getUserData($user);
        
        $this->success(['user' => $userData]);
    }
    
    /**
     * Genera un token único
     */
    private function generateToken(User $user): string
    {
        $payload = [
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (86400 * 30), // 30 días
        ];
        
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($payload));
        $secret = $this->getSecretKey();
        
        $signature = hash_hmac('sha256', "$header.$payload", $secret);
        
        return "$header.$payload.$signature";
    }
    
    /**
     * Guarda el token en la base de datos
     */
    private function storeToken(int $userId, string $token): void
    {
        $db = Database::getInstance();
        
        // Crear tabla si no existe
        $db->query("CREATE TABLE IF NOT EXISTS api_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(500) NOT NULL,
            device_name VARCHAR(255) DEFAULT NULL,
            device_info TEXT DEFAULT NULL,
            last_used_at DATETIME DEFAULT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_token (token(255)),
            INDEX idx_user_id (user_id)
        )");
        
        $db->insert('api_tokens', [
            'user_id' => $userId,
            'token' => hash('sha256', $token),
            'device_name' => $this->input('device_name') ?? 'Mobile App',
            'device_info' => json_encode([
                'platform' => $this->input('platform') ?? 'unknown',
                'version' => $this->input('app_version') ?? '1.0.0',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]),
            'expires_at' => date('Y-m-d H:i:s', time() + (86400 * 30)),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
    /**
     * Valida un token y retorna el user_id
     */
    public function validateToken(string $token): ?int
    {
        // Verificar firma del token
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        
        [$header, $payload, $signature] = $parts;
        
        $expectedSignature = hash_hmac('sha256', "$header.$payload", $this->getSecretKey());
        
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
        
        return $data['user_id'];
    }
    
    /**
     * Invalida un token
     */
    private function invalidateToken(string $token): void
    {
        $db = Database::getInstance();
        $tokenHash = hash('sha256', $token);
        
        $db->query("DELETE FROM api_tokens WHERE token = ?", [$tokenHash]);
    }
    
    /**
     * Obtiene el token Bearer del header
     */
    private function getBearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        
        // Fallback para servidores que no pasan Authorization
        if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
            return $_SERVER['HTTP_X_AUTH_TOKEN'];
        }
        
        return null;
    }
    
    /**
     * Obtiene la clave secreta para firmar tokens
     */
    private function getSecretKey(): string
    {
        // Idealmente esto debería estar en un archivo .env
        return $_ENV['API_SECRET'] ?? 'presentismo_api_secret_key_2024_change_this_in_production';
    }
    
    /**
     * Obtiene datos completos del usuario para la respuesta
     */
    private function getUserData(User $user): array
    {
        $shift = $user->currentShift();
        $clockStatus = $user->getClockStatusDetails();
        
        return [
            'id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'employee_code' => $user->employee_code,
            'dni' => $user->dni,
            'phone' => $user->phone,
            'position' => $user->position,
            'department' => $user->department()?->name ?? null,
            'work_center' => $user->workCenter()?->name ?? null,
            'work_center_id' => $user->work_center_id,
            'avatar_url' => $user->avatar_path ? "/uploads/avatars/{$user->avatar_path}" : null,
            'role' => [
                'id' => $user->role_id,
                'name' => $user->role()?->name ?? 'Empleado',
            ],
            'shift' => $shift ? [
                'id' => $shift->id,
                'name' => $shift->name,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'working_days' => $shift->getWorkingDays(),
            ] : null,
            'clock_status' => $clockStatus,
            'is_active' => (bool) $user->is_active,
            'language' => $user->language ?? 'es',
        ];
    }
}
