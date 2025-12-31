<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\Auth;
use Core\Translator;

class LanguageController extends Controller
{
    /**
     * Cambiar el idioma del usuario
     */
    public function change()
    {
        // Obtener el idioma de POST o GET
        $language = $_POST['language'] ?? $_GET['language'] ?? 'es';

        // Validar CSRF solo para POST
        if (!empty($_POST) && !$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            return $this->error('Token inválido');
        }

        // Validar que sea un idioma soportado
        $config = config('language');
        if (!isset($config['supported'][$language])) {
            return $this->error('Idioma no soportado');
        }

        // Cambiar el idioma en sesión AQUÍ
        $_SESSION['language'] = $language;

        // Si el usuario está autenticado, guardar en BD
        if (\Core\Auth::check()) {
            try {
                $user = \Core\Auth::user();
                if ($user && is_object($user) && method_exists($user, 'update')) {
                    $user->update(['language' => $language]);
                }
            } catch (\Exception $e) {
                // No fallar si hay error en BD, ya está guardado en sesión
            }
        }

        // Guardar mensaje de éxito
        Session::flash('success', 'Idioma cambiado correctamente');

        // Redirigir al referrer o dashboard
        $referer = $_SERVER['HTTP_REFERER'] ?? '/presentismo/public/index.php?route=dashboard';
        
        // Headers para asegurar que no se cachee
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Validar CSRF token
     */
    private function validateCsrf($token)
    {
        return $token === ($_SESSION['csrf_token'] ?? null);
    }

    /**
     * Enviar respuesta de éxito
     */
    private function success($message)
    {
        Session::flash('success', $message);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/presentismo/public/index.php?route=dashboard';
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Enviar respuesta de error
     */
    private function error($message)
    {
        Session::flash('error', $message);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/presentismo/public/index.php?route=dashboard';
        header('Location: ' . $referer);
        exit;
    }
}
