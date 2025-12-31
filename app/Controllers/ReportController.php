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

        // Obtener fichajes con entrada y salida emparejadas
        $timeEntries = $db->fetchAll(
            "SELECT 
                te_in.id,
                te_in.user_id,
                u.name,
                te_in.recorded_at as clock_in_time,
                te_out.recorded_at as clock_out_time,
                te_in.latitude as clock_in_latitude,
                te_in.longitude as clock_in_longitude,
                te_out.latitude as clock_out_latitude,
                te_out.longitude as clock_out_longitude,
                te_in.notes,
                te_in.source,
                te_in.metadata,
                TIMESTAMPDIFF(MINUTE, te_in.recorded_at, COALESCE(te_out.recorded_at, NOW())) as total_minutes
             FROM time_entries te_in
             JOIN users u ON te_in.user_id = u.id
             LEFT JOIN time_entries te_out ON te_out.user_id = te_in.user_id 
                 AND te_out.type = 'clock_out' 
                 AND DATE(te_out.recorded_at) = DATE(te_in.recorded_at)
                 AND te_out.recorded_at > te_in.recorded_at
                 AND te_out.id = (
                     SELECT MIN(id) FROM time_entries 
                     WHERE user_id = te_in.user_id 
                     AND type = 'clock_out' 
                     AND recorded_at > te_in.recorded_at
                     AND DATE(recorded_at) = DATE(te_in.recorded_at)
                 )
             WHERE te_in.tenant_id = ? 
             AND te_in.type = 'clock_in'
             AND DATE(te_in.recorded_at) BETWEEN ? AND ?
             ORDER BY te_in.recorded_at DESC",
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
             LEFT JOIN absence_types at ON a.absence_type_id = at.id
             WHERE a.tenant_id = ? AND DATE(a.start_date) BETWEEN ? AND ?
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

        // Calcular horas por empleado usando la estructura correcta de time_entries
        $hours = $db->fetchAll(
            "SELECT 
                u.id, 
                u.name,
                COALESCE(
                    (SELECT SUM(
                        TIMESTAMPDIFF(MINUTE, te_in.recorded_at, 
                            COALESCE(
                                (SELECT MIN(te_out.recorded_at) 
                                 FROM time_entries te_out 
                                 WHERE te_out.user_id = te_in.user_id 
                                 AND te_out.type = 'clock_out' 
                                 AND te_out.recorded_at > te_in.recorded_at
                                 AND DATE(te_out.recorded_at) = DATE(te_in.recorded_at)),
                                NOW()
                            )
                        )
                    ) / 60
                    FROM time_entries te_in 
                    WHERE te_in.user_id = u.id 
                    AND te_in.type = 'clock_in'
                    AND DATE(te_in.recorded_at) BETWEEN ? AND ?), 
                0) as total_hours,
                (SELECT COUNT(*) FROM time_entries 
                 WHERE user_id = u.id AND type = 'clock_in' 
                 AND DATE(recorded_at) BETWEEN ? AND ?) as entries
             FROM users u
             WHERE u.tenant_id = ? AND u.deleted_at IS NULL AND u.is_active = 1
             GROUP BY u.id, u.name
             ORDER BY u.name ASC",
            [$startDate, $endDate, $startDate, $endDate, $tenantId]
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
