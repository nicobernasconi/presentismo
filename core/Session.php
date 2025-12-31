<?php
namespace Core;

/**
 * Session - Helpers para manejo de sesión
 */
class Session
{
    /**
     * Obtiene un valor de sesión
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Establece un valor en sesión
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Verifica si existe una clave
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Elimina un valor de sesión
     */
    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Obtiene y elimina (flash)
     */
    public static function flash(string $key, $default = null)
    {
        $value = self::get($key, $default);
        self::forget($key);
        return $value;
    }

    /**
     * Obtiene errores flash
     */
    public static function errors(): array
    {
        return self::flash('errors', []);
    }

    /**
     * Obtiene valor antiguo
     */
    public static function old(string $key, $default = null)
    {
        $old = self::get('old', []);
        $value = $old[$key] ?? $default;
        return $value;
    }

    /**
     * Limpia datos antiguos
     */
    public static function clearOld(): void
    {
        self::forget('old');
    }

    /**
     * Obtiene mensaje de éxito
     */
    public static function success(): ?string
    {
        return self::flash('success');
    }

    /**
     * Obtiene mensaje de error
     */
    public static function error(): ?string
    {
        return self::flash('error');
    }

    /**
     * Genera token CSRF
     */
    public static function csrf(): string
    {
        if (!self::has('csrf_token')) {
            self::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return self::get('csrf_token');
    }

    /**
     * Valida token CSRF
     */
    public static function validateCsrf(string $token): bool
    {
        return hash_equals(self::get('csrf_token', ''), $token);
    }
}
