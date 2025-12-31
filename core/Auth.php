<?php
namespace Core;

/**
 * Auth - Manejo de autenticación
 */
class Auth
{
    /**
     * Intenta autenticar un usuario
     */
    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        
        $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1 AND deleted_at IS NULL";
        $user = $db->fetch($sql, [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        self::login($user);
        return true;
    }

    /**
     * Inicia sesión con los datos del usuario
     */
    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['tenant_id'] = $user['tenant_id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'tenant_id' => $user['tenant_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
        ];

        // Actualizar último login
        Database::getInstance()->update(
            'users',
            ['last_login_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$user['id']]
        );
    }

    /**
     * Cierra la sesión
     */
    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Verifica si hay un usuario autenticado
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Obtiene el usuario autenticado
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Obtiene el ID del usuario
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtiene el tenant_id actual
     */
    public static function tenantId(): ?int
    {
        return $_SESSION['tenant_id'] ?? null;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     */
    public static function hasRole(string $role): bool
    {
        $config = require CONFIG_PATH . '/app.php';
        $roleId = $config['roles'][$role] ?? null;
        
        return $roleId && ($_SESSION['user']['role_id'] ?? null) == $roleId;
    }

    /**
     * Verifica si es super admin
     */
    public static function isSuperAdmin(): bool
    {
        return self::hasRole('super_admin');
    }

    /**
     * Verifica si es admin
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('super_admin') || self::hasRole('admin');
    }

    /**
     * Verifica si es supervisor
     */
    public static function isSupervisor(): bool
    {
        return self::hasRole('super_admin') || self::hasRole('admin') || self::hasRole('supervisor');
    }

    /**
     * Hash de contraseña
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
