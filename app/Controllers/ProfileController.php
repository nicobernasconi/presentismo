<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        $userId = Auth::id();
        $db = Database::getInstance();

        $user = $db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [$userId]
        );

        $this->view('profile.index', [
            'title' => 'Mi Perfil',
            'profileUser' => $user,
        ]);
    }

    public function update(): void
    {
        $userId = Auth::id();
        $db = Database::getInstance();

        $data = [
            'name' => $this->input('name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'employee_code' => $this->input('employee_code'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->update('users', $data, "id = ?", [$userId]);
        $this->withSuccess('Perfil actualizado exitosamente');
        $this->redirect('/perfil');
    }

    public function updatePassword(): void
    {
        $userId = Auth::id();
        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');

        $db = Database::getInstance();

        // Verificar contraseña actual
        $user = $db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);

        if (!password_verify($currentPassword, $user['password'] ?? '')) {
            $this->withError('La contraseña actual es incorrecta');
            $this->redirect('/perfil');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->withError('Las contraseñas no coinciden');
            $this->redirect('/perfil');
            return;
        }

        if (strlen($newPassword) < 8) {
            $this->withError('La contraseña debe tener al menos 8 caracteres');
            $this->redirect('/perfil');
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $db->update('users', ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')], "id = ?", [$userId]);

        $this->withSuccess('Contraseña actualizada exitosamente');
        $this->redirect('/perfil');
    }
}
