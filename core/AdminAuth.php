<?php
namespace Core;

class AdminAuth
{
    private static ?array $admin = null;

    public static function authenticate(string $email, string $password): bool
    {
        $db = Database::getInstance();
        
        $admin = $db->fetch(
            "SELECT * FROM admin_users WHERE email = ? AND is_active = 1 AND deleted_at IS NULL",
            [$email]
        );

        if (!$admin || !password_verify($password, $admin['password'])) {
            return false;
        }

        // Actualizar último login
        $db->query("UPDATE admin_users SET last_login_at = NOW() WHERE id = ?", [$admin['id']]);

        // Guardar en sesión
        $_SESSION['admin_user'] = $admin;
        self::$admin = $admin;

        return true;
    }

    public static function check(): bool
    {
        if (self::$admin !== null) {
            return true;
        }

        if (isset($_SESSION['admin_user'])) {
            self::$admin = $_SESSION['admin_user'];
            return true;
        }

        return false;
    }

    public static function user(): ?array
    {
        if (self::$admin === null && isset($_SESSION['admin_user'])) {
            self::$admin = $_SESSION['admin_user'];
        }

        return self::$admin;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }

    public static function email(): ?string
    {
        $user = self::user();
        return $user['email'] ?? null;
    }

    public static function name(): ?string
    {
        $user = self::user();
        return $user['name'] ?? null;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_user']);
        self::$admin = null;
    }

    public static function guard(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Acceso denegado. Por favor inicia sesión.');
            header('Location: /presentismo/public/admin/login');
            exit;
        }
    }
}
