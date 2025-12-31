<?php
namespace App\Middleware;

use Core\AdminAuth;
use Core\Session;

class AdminAuthMiddleware
{
    public function handle(): bool
    {
        if (!AdminAuth::check()) {
            Session::flash('error', 'Acceso denegado. Por favor inicia sesión.');
            header('Location: /presentismo/public/admin/login');
            exit;
        }

        return true;
    }
}
