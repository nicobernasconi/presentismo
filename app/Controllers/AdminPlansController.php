<?php
namespace App\Controllers;

use Core\Controller;
use Core\AdminAuth;
use Core\Database;
use Core\Session;

class AdminPlansController extends Controller
{
    public function __construct()
    {
        AdminAuth::guard();
        $this->setLayout('admin.layouts.app');
    }

    public function index(): void
    {
        $db = Database::getInstance();

        $plans = $db->fetchAll(
            "SELECT * FROM plans WHERE deleted_at IS NULL ORDER BY max_employees ASC"
        );

        $this->view('admin.plans.index', [
            'title' => 'Gestión de Planes',
            'plans' => $plans,
        ]);
    }

    public function create(): void
    {
        $this->view('admin.plans.form', [
            'title' => 'Crear Plan',
            'plan' => null,
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /presentismo/public/admin/planes');
            exit;
        }

        $db = Database::getInstance();

        $name = $this->input('name');
        $description = $this->input('description');
        $max_employees = $this->input('max_employees');
        $monthly_price = $this->input('monthly_price');
        $annual_price = $this->input('annual_price');

        if (!$name || !$max_employees) {
            Session::flash('error', 'Nombre y máximo de empleados son requeridos');
            header('Location: /presentismo/public/admin/planes/crear');
            exit;
        }

        $features = json_encode(['max_employees' => intval($max_employees)]);

        $db->query(
            "INSERT INTO plans (name, description, max_employees, monthly_price, annual_price, features) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$name, $description, $max_employees, $monthly_price, $annual_price, $features]
        );

        Session::flash('success', 'Plan creado exitosamente');
        header('Location: /presentismo/public/admin/planes');
        exit;
    }

    public function edit(): void
    {
        $id = $this->query('id');
        $db = Database::getInstance();

        $plan = $db->fetch(
            "SELECT * FROM plans WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );

        if (!$plan) {
            Session::flash('error', 'Plan no encontrado');
            header('Location: /presentismo/public/admin/planes');
            exit;
        }

        $this->view('admin.plans.form', [
            'title' => 'Editar Plan',
            'plan' => $plan,
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /presentismo/public/admin/planes');
            exit;
        }

        $id = $this->input('id');
        $name = $this->input('name');
        $description = $this->input('description');
        $max_employees = $this->input('max_employees');
        $monthly_price = $this->input('monthly_price');
        $annual_price = $this->input('annual_price');
        $is_active = $this->input('is_active') ? 1 : 0;

        $db = Database::getInstance();

        $db->query(
            "UPDATE plans SET name = ?, description = ?, max_employees = ?, 
             monthly_price = ?, annual_price = ?, is_active = ? WHERE id = ?",
            [$name, $description, $max_employees, $monthly_price, $annual_price, $is_active, $id]
        );

        Session::flash('success', 'Plan actualizado exitosamente');
        header('Location: /presentismo/public/admin/planes');
        exit;
    }

    public function destroy(): void
    {
        $id = $this->input('id');
        $db = Database::getInstance();

        $db->query("UPDATE plans SET deleted_at = NOW() WHERE id = ?", [$id]);

        Session::flash('success', 'Plan eliminado exitosamente');
        header('Location: /presentismo/public/admin/planes');
        exit;
    }
}
