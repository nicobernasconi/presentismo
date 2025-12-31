<?php
namespace App\Controllers;

use Core\Controller;
use Core\AdminAuth;
use Core\Session;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        // No usar layout para login
    }

    public function login(): void
    {
        // Si ya está logueado, redirigir a dashboard
        if (AdminAuth::check()) {
            header('Location: /presentismo/public/admin/dashboard');
            exit;
        }

        $this->view('admin.auth.login', [
            'title' => 'Admin Login',
            'error' => Session::get('error'),
            'success' => Session::get('success'),
        ]);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /presentismo/public/admin/login');
            exit;
        }

        $email = $this->input('email');
        $password = $this->input('password');

        if (!$email || !$password) {
            Session::flash('error', 'Email y contraseña son requeridos');
            header('Location: /presentismo/public/admin/login');
            exit;
        }

        if (AdminAuth::authenticate($email, $password)) {
            Session::flash('success', 'Bienvenido ' . AdminAuth::name());
            header('Location: /presentismo/public/admin/dashboard');
            exit;
        }

        Session::flash('error', 'Email o contraseña incorrectos');
        header('Location: /presentismo/public/admin/login');
        exit;
    }

    public function logout(): void
    {
        AdminAuth::logout();
        Session::flash('success', 'Sesión cerrada');
        header('Location: /presentismo/public/admin/login');
        exit;
    }
}
