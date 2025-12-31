<?php

namespace App\Controllers;

use Core\Controller;
use Core\AdminAuth;
use Core\Database;
use Core\Session;
use App\Models\User;

/**
 * Controlador para crear usuarios superadmin por empresa (desde panel admin)
 */
class AdminUsersController extends Controller
{
    public function __construct()
    {
        AdminAuth::guard();
        $this->setLayout('admin.layouts.app');
    }

    /**
     * Lista de superadmins por empresa
     */
    public function index(): void
    {
        $db = Database::getInstance();

        $sql = "SELECT u.id, u.tenant_id, u.name, u.email, u.is_active, u.created_at, t.name as tenant_name 
                FROM users u
                JOIN tenants t ON u.tenant_id = t.id
                WHERE u.role_id = 1 AND u.deleted_at IS NULL
                ORDER BY u.created_at DESC";

        $users = $db->fetchAll($sql);

        $this->view('admin.users.index', [
            'title' => 'Superadmins por Empresa',
            'users' => $users,
        ]);
    }

    /**
     * Formulario para crear superadmin
     */
    public function create(): void
    {
        $db = Database::getInstance();
        
        $companies = $db->fetchAll(
            "SELECT id, name FROM tenants WHERE deleted_at IS NULL ORDER BY name ASC"
        );

        $this->view('admin.users.form', [
            'title' => 'Crear Superadmin de Empresa',
            'user' => null,
            'companies' => $companies,
        ]);
    }

    /**
     * Guarda nuevo superadmin
     */
    public function store(): void
    {
        $db = Database::getInstance();

        $tenantId = $this->input('tenant_id');
        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');

        // Validar
        $errors = $this->validate(['email' => $email, 'password' => $password], [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors) || !$tenantId || !$name) {
            Session::flash('error', 'Campos requeridos: Empresa, Nombre, Email y Contraseña (mín 6 caracteres)');
            header('Location: /presentismo/public/admin/usuarios/crear');
            exit;
        }

        // Verificar que la empresa existe
        $company = $db->fetch("SELECT id FROM tenants WHERE id = ? AND deleted_at IS NULL", [$tenantId]);
        if (!$company) {
            Session::flash('error', 'Empresa no encontrada');
            header('Location: /presentismo/public/admin/usuarios/crear');
            exit;
        }

        // Verificar email único dentro del tenant (constraint de BD lo valida, pero lo hacemos explícito)
        $existing = $db->fetch(
            "SELECT id FROM users WHERE tenant_id = ? AND email = ? AND deleted_at IS NULL",
            [$tenantId, $email]
        );
        if ($existing) {
            Session::flash('error', 'Ya existe un usuario con ese email en esta empresa');
            header('Location: /presentismo/public/admin/usuarios/crear');
            exit;
        }

        // Crear usuario superadmin (role_id = 1)
        // Asegurarse de que tiene los datos esenciales
        $db->insert('users', [
            'tenant_id' => $tenantId,
            'role_id' => 1,  // Superadmin
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'name' => $name,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', "Superadmin '{$name}' creado exitosamente en la empresa");
        header('Location: /presentismo/public/admin/usuarios');
        exit;
    }

    /**
     * Editar superadmin
     */
    public function edit($id): void
    {
        $db = Database::getInstance();

        $user = $db->fetch(
            "SELECT u.*, t.name as tenant_name FROM users u
             JOIN tenants t ON u.tenant_id = t.id
             WHERE u.id = ? AND u.role_id = 1 AND u.deleted_at IS NULL",
            [$id]
        );

        if (!$user) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: /presentismo/public/admin/usuarios');
            exit;
        }

        $companies = $db->fetchAll(
            "SELECT id, name FROM tenants WHERE deleted_at IS NULL ORDER BY name ASC"
        );

        $this->view('admin.users.form', [
            'title' => 'Editar Superadmin',
            'user' => $user,
            'companies' => $companies,
        ]);
    }

    /**
     * Actualizar superadmin
     */
    public function update($id): void
    {
        $db = Database::getInstance();

        $user = $db->fetch(
            "SELECT id FROM users WHERE id = ? AND role_id = 1 AND deleted_at IS NULL",
            [$id]
        );

        if (!$user) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: /presentismo/public/admin/usuarios');
            exit;
        }

        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');

        if (!$name || !$email) {
            Session::flash('error', 'Nombre y email son requeridos');
            header('Location: /presentismo/public/admin/usuarios/' . $id . '/editar');
            exit;
        }

        $currentUser = $db->fetch("SELECT email FROM users WHERE id = ?", [$id]);
        
        // Verificar email único si cambió
        if ($email !== $currentUser['email']) {
            $existing = $db->fetch(
                "SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL",
                [$email, $id]
            );
            if ($existing) {
                Session::flash('error', 'Ya existe otro usuario con ese email');
                header('Location: /presentismo/public/admin/usuarios/' . $id . '/editar');
                exit;
            }
        }

        $updateData = [
            'name' => $name,
            'email' => $email,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $db->update('users', $updateData, 'id = ?', [$id]);

        Session::flash('success', 'Superadmin actualizado correctamente');
        header('Location: /presentismo/public/admin/usuarios');
        exit;
    }

    /**
     * Eliminar superadmin
     */
    public function destroy($id): void
    {
        $db = Database::getInstance();

        $user = $db->fetch(
            "SELECT id FROM users WHERE id = ? AND role_id = 1 AND deleted_at IS NULL",
            [$id]
        );

        if (!$user) {
            Session::flash('error', 'Usuario no encontrado');
            header('Location: /presentismo/public/admin/usuarios');
            exit;
        }

        $db->update('users', ['deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);

        Session::flash('success', 'Superadmin eliminado correctamente');
        header('Location: /presentismo/public/admin/usuarios');
        exit;
    }
}
