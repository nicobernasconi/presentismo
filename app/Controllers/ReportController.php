<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        $this->view('reports.index', [
            'title' => 'Reportes',
        ]);
    }

    public function timeEntries(): void
    {
        if (!Auth::isSupervisor()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();
        $startDate = $this->query('start_date', date('Y-m-01'));
        $endDate = $this->query('end_date', date('Y-m-d'));

        $timeEntries = $db->fetchAll(
            "SELECT te.*, u.name FROM time_entries te
             JOIN users u ON te.user_id = u.id
             WHERE te.tenant_id = ? AND DATE(te.clock_in_time) BETWEEN ? AND ? AND te.deleted_at IS NULL
             ORDER BY te.clock_in_time DESC",
            [$tenantId, $startDate, $endDate]
        );

        $this->view('reports.time_entries', [
            'title' => 'Reporte de Fichajes',
            'timeEntries' => $timeEntries,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function absences(): void
    {
        if (!Auth::isSupervisor()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();
        $startDate = $this->query('start_date', date('Y-m-01'));
        $endDate = $this->query('end_date', date('Y-m-d'));

        $absences = $db->fetchAll(
            "SELECT a.*, u.name, at.name as type_name FROM absences a
             JOIN users u ON a.user_id = u.id
             JOIN absence_types at ON a.absence_type_id = at.id
             WHERE a.tenant_id = ? AND DATE(a.start_date) BETWEEN ? AND ? AND a.deleted_at IS NULL
             ORDER BY a.start_date DESC",
            [$tenantId, $startDate, $endDate]
        );

        $this->view('reports.absences', [
            'title' => 'Reporte de Ausencias',
            'absences' => $absences,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function hours(): void
    {
        if (!Auth::isSupervisor()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();
        $month = $this->query('month', date('m'));
        $year = $this->query('year', date('Y'));

        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $hours = $db->fetchAll(
            "SELECT u.id, u.name,
                    SUM(TIMESTAMPDIFF(HOUR, te.clock_in_time, COALESCE(te.clock_out_time, NOW()))) as total_hours,
                    COUNT(*) as entries
             FROM users u
             LEFT JOIN time_entries te ON u.id = te.user_id AND DATE(te.clock_in_time) BETWEEN ? AND ?
             WHERE u.tenant_id = ? AND u.deleted_at IS NULL
             GROUP BY u.id, u.name
             ORDER BY u.name ASC",
            [$startDate, $endDate, $tenantId]
        );

        $this->view('reports.hours', [
            'title' => 'Reporte de Horas',
            'hours' => $hours,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function export(): void
    {
        if (!Auth::isSupervisor()) {
            $this->redirect('/dashboard');
            return;
        }

        $type = $this->query('type', 'time_entries');
        $startDate = $this->query('start_date', date('Y-m-01'));
        $endDate = $this->query('end_date', date('Y-m-d'));

        // Implementar lógica de exportación (CSV, Excel, PDF, etc.)
        $this->withSuccess('Exportación completada');
        $this->redirect('/reportes');
    }
}
