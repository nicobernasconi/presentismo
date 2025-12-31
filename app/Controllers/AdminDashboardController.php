<?php
namespace App\Controllers;

use Core\Controller;
use Core\AdminAuth;
use Core\Database;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        AdminAuth::guard();
        $this->setLayout('admin.layouts.app');
    }

    public function index(): void
    {
        $db = Database::getInstance();

        // Estadísticas
        $totalCompanies = $db->fetch(
            "SELECT COUNT(*) as count FROM tenants WHERE deleted_at IS NULL"
        )['count'] ?? 0;

        $activeCompanies = $db->fetch(
            "SELECT COUNT(*) as count FROM tenants WHERE is_active = 1 AND deleted_at IS NULL"
        )['count'] ?? 0;

        $totalEmployees = $db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL"
        )['count'] ?? 0;

        $totalPlans = $db->fetch(
            "SELECT COUNT(*) as count FROM plans WHERE is_active = 1 AND deleted_at IS NULL"
        )['count'] ?? 0;

        // Empresas recientes
        $recentCompanies = $db->fetchAll(
            "SELECT t.id, t.name, t.email, t.is_active, p.name as plan_name FROM tenants t
             LEFT JOIN plans p ON t.plan_id = p.id
             WHERE t.deleted_at IS NULL
             ORDER BY t.created_at DESC LIMIT 10"
        );

        // Distribución por plan
        $planDistribution = $db->fetchAll(
            "SELECT p.name, COUNT(t.id) as count FROM plans p
             LEFT JOIN tenants t ON p.id = t.plan_id AND t.deleted_at IS NULL
             WHERE p.deleted_at IS NULL
             GROUP BY p.id ORDER BY COUNT(t.id) DESC"
        );

        $this->view('admin.dashboard.index', [
            'title' => 'Panel Administrativo',
            'statistics' => [
                'total_companies' => $totalCompanies,
                'active_companies' => $activeCompanies,
                'total_employees' => $totalEmployees,
                'total_plans' => $totalPlans,
            ],
            'recent_companies' => $recentCompanies,
            'plan_distribution' => $planDistribution,
        ]);
    }
}
