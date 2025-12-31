<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\User;
use App\Models\TimeEntry;
use App\Models\Absence;
use App\Models\Project;
use Core\Database;

/**
 * Controlador del Dashboard
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Muestra el dashboard principal
     */
    public function index(): void
    {
        $user = Auth::user();
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        // Si el usuario no tiene empresa asignada, mostrar página de espera
        if (!$tenantId) {
            $this->view('dashboard.no_tenant', [
                'title' => 'Bienvenido al Sistema',
            ]);
            return;
        }

        // Estado actual de fichaje (usar getClockStatusDetails para array completo)
        /** @var User $currentUser */
        $currentUser = User::find($userId);
        $clockStatus = $currentUser->getClockStatusDetails();
        
        // Fichajes de hoy
        $todayEntries = TimeEntry::getTodayEntries($userId);
        
        // Horas trabajadas hoy
        $todayHours = TimeEntry::calculateDailyHours($userId, date('Y-m-d'));
        
        // Resumen semanal
        $weeklyHours = TimeEntry::getWeeklySummary($userId);
        $weeklyTotal = array_sum($weeklyHours);

        // Estadísticas según rol
        $stats = [];
        
        if (Auth::isAdmin()) {
            // Empleados activos
            $stats['total_employees'] = User::count('is_active = 1 AND role_id != 1');
            
            // Fichados hoy
            $sql = "SELECT COUNT(DISTINCT user_id) as count 
                    FROM time_entries 
                    WHERE tenant_id = ? AND DATE(recorded_at) = CURDATE()";
            $result = $db->fetch($sql, [$tenantId]);
            $stats['clocked_in_today'] = $result['count'] ?? 0;
            
            // Ausencias pendientes
            $stats['pending_absences'] = Absence::count("status = 'pending'");
            
            // Proyectos activos
            $stats['active_projects'] = Project::count("status = 'active'");
        }

        // Próximas ausencias del usuario
        $sql = "SELECT a.*, at.name as type_name, at.color 
                FROM absences a
                JOIN absence_types at ON a.absence_type_id = at.id
                WHERE a.user_id = ? 
                AND a.status = 'approved'
                AND a.start_date >= CURDATE()
                ORDER BY a.start_date ASC
                LIMIT 5";
        $upcomingAbsences = $db->fetchAll($sql, [$userId]);

        // Ausencias pendientes de aprobación (para supervisores/admins)
        $pendingAbsences = [];
        if (Auth::isSupervisor()) {
            $sql = "SELECT a.*, u.name as user_name, at.name as type_name, at.color
                    FROM absences a
                    JOIN users u ON a.user_id = u.id
                    JOIN absence_types at ON a.absence_type_id = at.id
                    WHERE a.tenant_id = ? AND a.status = 'pending'
                    ORDER BY a.created_at ASC
                    LIMIT 10";
            $pendingAbsences = $db->fetchAll($sql, [$tenantId]);
        }

        // Turno actual
        $currentShift = $currentUser->currentShift();

        $this->view('dashboard.index', [
            'title' => 'Dashboard',
            'user' => $user,
            'currentUser' => $currentUser,
            'clockStatus' => $clockStatus,
            'todayEntries' => $todayEntries,
            'todayHours' => $todayHours,
            'weeklyHours' => $weeklyHours,
            'weeklyTotal' => $weeklyTotal,
            'stats' => $stats,
            'upcomingAbsences' => $upcomingAbsences,
            'pendingAbsences' => $pendingAbsences,
            'currentShift' => $currentShift,
        ]);
    }
}
