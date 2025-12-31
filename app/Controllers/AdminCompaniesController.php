<?php
namespace App\Controllers;

use Core\Controller;
use Core\AdminAuth;
use Core\Database;
use Core\Session;

class AdminCompaniesController extends Controller
{
    public function __construct()
    {
        AdminAuth::guard();
        $this->setLayout('admin.layouts.app');
    }

    public function index(): void
    {
        $db = Database::getInstance();

        $companies = $db->fetchAll(
            "SELECT t.*, p.name as plan_name,
                    u.name as superadmin_name, u.email as superadmin_email
             FROM tenants t 
             LEFT JOIN plans p ON t.plan_id = p.id
             LEFT JOIN users u ON u.tenant_id = t.id AND u.role_id = 1 AND u.deleted_at IS NULL
             WHERE t.deleted_at IS NULL 
             ORDER BY t.created_at DESC"
        );

        $this->view('admin.companies.index', [
            'title' => 'Gestión de Empresas',
            'companies' => $companies,
        ]);
    }

    public function create(): void
    {
        $db = Database::getInstance();
        
        $plans = $db->fetchAll(
            "SELECT id, name, max_employees FROM plans WHERE is_active = 1 AND deleted_at IS NULL ORDER BY max_employees ASC"
        );

        $this->view('admin.companies.form', [
            'title' => 'Crear Empresa',
            'company' => null,
            'plans' => $plans,
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /presentismo/public/admin/empresas');
            exit;
        }

        $db = Database::getInstance();

        $name = $this->input('name');
        $slug = strtolower(str_replace(' ', '-', $name));
        $tax_id = $this->input('tax_id');
        $email = $this->input('email');
        $phone = $this->input('phone');
        $address = $this->input('address');
        $city = $this->input('city');
        $plan_id = $this->input('plan_id');

        if (!$name || !$tax_id || !$email || !$plan_id) {
            Session::flash('error', 'Campos requeridos: Nombre, RFC, Email y Plan');
            header('Location: /presentismo/public/admin/empresas/crear');
            exit;
        }

        // Verificar que el RFC no esté repetido
        $existing = $db->fetch("SELECT id FROM tenants WHERE tax_id = ? AND deleted_at IS NULL", [$tax_id]);
        if ($existing) {
            Session::flash('error', 'Ya existe una empresa con ese RFC');
            header('Location: /presentismo/public/admin/empresas/crear');
            exit;
        }

        $db->query(
            "INSERT INTO tenants (name, slug, tax_id, email, phone, address, city, plan_id, subscription_start_at, is_active) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)",
            [$name, $slug, $tax_id, $email, $phone, $address, $city, $plan_id]
        );

        Session::flash('success', 'Empresa creada exitosamente');
        header('Location: /presentismo/public/admin/empresas');
        exit;
    }

    public function edit($id): void
    {
        $db = Database::getInstance();

        $company = $db->fetch(
            "SELECT * FROM tenants WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );

        if (!$company) {
            Session::flash('error', 'Empresa no encontrada');
            header('Location: /presentismo/public/admin/empresas');
            exit;
        }

        $plans = $db->fetchAll(
            "SELECT id, name, max_employees FROM plans WHERE is_active = 1 AND deleted_at IS NULL ORDER BY max_employees ASC"
        );

        $this->view('admin.companies.form', [
            'title' => 'Editar Empresa',
            'company' => $company,
            'plans' => $plans,
        ]);
    }

    public function update($idParam = null): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /presentismo/public/admin/empresas');
            exit;
        }
        // Preferir ID de la ruta si está presente; si no, usar el enviado por POST
        $id = $idParam ?? $this->input('id');
        $name = $this->input('name');
        $tax_id = $this->input('tax_id');
        $email = $this->input('email');
        $phone = $this->input('phone');
        $address = $this->input('address');
        $city = $this->input('city');
        $plan_id = $this->input('plan_id');
        $is_active = $this->input('is_active') ? 1 : 0;

        $db = Database::getInstance();

        // Verificar RFC duplicado
        $existing = $db->fetch(
            "SELECT id FROM tenants WHERE tax_id = ? AND id != ? AND deleted_at IS NULL",
            [$tax_id, $id]
        );
        if ($existing) {
            Session::flash('error', 'Ya existe otra empresa con ese RFC');
            header('Location: /presentismo/public/admin/empresas/' . $id . '/editar');
            exit;
        }

        $db->query(
            "UPDATE tenants SET name = ?, tax_id = ?, email = ?, phone = ?, address = ?, city = ?, plan_id = ?, is_active = ? WHERE id = ?",
            [$name, $tax_id, $email, $phone, $address, $city, $plan_id, $is_active, $id]
        );

        Session::flash('success', 'Empresa actualizada exitosamente');
        header('Location: /presentismo/public/admin/empresas');
        exit;
    }

    public function show($id): void
    {
        $db = Database::getInstance();

        $company = $db->fetch(
            "SELECT t.*, p.name as plan_name, p.max_employees FROM tenants t
             LEFT JOIN plans p ON t.plan_id = p.id
             WHERE t.id = ? AND t.deleted_at IS NULL",
            [$id]
        );

        if (!$company) {
            Session::flash('error', 'Empresa no encontrada');
            header('Location: /presentismo/public/admin/empresas');
            exit;
        }

        // Obtener empleados de la empresa
        $employees = $db->fetchAll(
            "SELECT id, name, email, is_active FROM users WHERE tenant_id = ? AND deleted_at IS NULL",
            [$id]
        );

        $this->view('admin.companies.show', [
            'title' => 'Detalles de Empresa',
            'company' => $company,
            'employees' => $employees,
        ]);
    }

    public function destroy($idParam = null): void
    {
        // Preferir ID de la ruta si está presente; si no, usar el enviado por POST
        $id = $idParam ?? $this->input('id');
        $db = Database::getInstance();

        $db->query("UPDATE tenants SET deleted_at = NOW() WHERE id = ?", [$id]);

        Session::flash('success', 'Empresa eliminada exitosamente');
        header('Location: /presentismo/public/admin/empresas');
        exit;
    }
}
