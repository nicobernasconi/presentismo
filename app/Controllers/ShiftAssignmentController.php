<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\User;
use App\Models\Shift;

class ShiftAssignmentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Solo administradores y supervisores pueden gestionar asignaciones
        if (!Auth::isAdmin() && !Auth::isSupervisor()) {
            $this->redirect('/dashboard');
        }
    }

    /**
     * Lista todas las asignaciones de turnos
     */
    public function index()
    {
        $tenantId = Auth::tenantId();
        
        $sql = "SELECT sa.*, 
                       u.name as employee_name, 
                       u.employee_code,
                       s.name as shift_name,
                       s.start_time,
                       s.end_time,
                       s.color
                FROM shift_assignments sa
                JOIN users u ON sa.user_id = u.id
                JOIN shifts s ON sa.shift_id = s.id
                WHERE sa.tenant_id = ?
                ORDER BY sa.start_date DESC, u.name ASC";
        
        $assignments = $this->db->fetchAll($sql, [$tenantId]);
        
        return $this->view('shifts/assignments/index', [
            'assignments' => $assignments
        ]);
    }

    /**
     * Formulario para asignar turno a un empleado
     */
    public function create(?int $userId = null)
    {
        $tenantId = Auth::tenantId();
        
        // Obtener empleados activos
        $employees = User::where('tenant_id', $tenantId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        
        // Obtener turnos activos
        $shifts = Shift::where('tenant_id', $tenantId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        
        return $this->view('shifts/assignments/form', [
            'employees' => $employees,
            'shifts' => $shifts,
            'selectedUserId' => $userId
        ]);
    }

    /**
     * Guardar asignación de turno
     */
    public function store()
    {
        $this->validateCsrfToken();
        
        $userId = (int)($_POST['user_id'] ?? 0);
        $shiftId = (int)($_POST['shift_id'] ?? 0);
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? null;
        $notes = $_POST['notes'] ?? null;
        $deactivateCurrent = isset($_POST['deactivate_current']);
        
        // Validaciones
        if (!$userId || !$shiftId || !$startDate) {
            $this->setFlashMessage('error', 'Todos los campos obligatorios deben completarse');
            return $this->redirect('/turnos/asignaciones/crear');
        }
        
        $user = User::find($userId);
        /** @var User|null $user */
        if (!$user || $user->tenant_id != Auth::tenantId()) {
            $this->setFlashMessage('error', 'Empleado no válido');
            return $this->redirect('/turnos/asignaciones');
        }
        
        $shift = Shift::find($shiftId);
        if (!$shift || $shift->tenant_id != Auth::tenantId()) {
            $this->setFlashMessage('error', 'Turno no válido');
            return $this->redirect('/turnos/asignaciones');
        }
        
        // Desactivar asignación actual si se solicita
        if ($deactivateCurrent) {
            $sql = "UPDATE shift_assignments 
                    SET is_active = 0, updated_at = NOW()
                    WHERE user_id = ? AND is_active = 1 AND end_date IS NULL";
            $this->db->query($sql, [$userId]);
        }
        
        // Verificar solapamiento de fechas
        if ($this->hasOverlappingAssignment($userId, $startDate, $endDate)) {
            $this->setFlashMessage('error', 'Ya existe una asignación activa en este período');
            return $this->redirect('/turnos/asignaciones/crear');
        }
        
        // Crear asignación
        $success = $user->assignShift($shiftId, $startDate, $endDate, $notes);
        
        if ($success) {
            $this->setFlashMessage('success', 'Turno asignado correctamente');
        } else {
            $this->setFlashMessage('error', 'Error al asignar el turno');
        }
        
        return $this->redirect('/turnos/asignaciones');
    }

    /**
     * Desactivar una asignación
     */
    public function deactivate(int $id)
    {
        $this->validateCsrfToken();
        
        $sql = "UPDATE shift_assignments 
                SET is_active = 0, updated_at = NOW()
                WHERE id = ? AND tenant_id = ?";
        
        $success = $this->db->query($sql, [$id, Auth::tenantId()])->rowCount() > 0;
        
        if ($success) {
            $this->setFlashMessage('success', 'Asignación desactivada');
        } else {
            $this->setFlashMessage('error', 'Error al desactivar la asignación');
        }
        
        return $this->redirect('/turnos/asignaciones');
    }

    /**
     * Eliminar una asignación
     */
    public function delete(int $id)
    {
        $this->validateCsrfToken();
        
        $sql = "DELETE FROM shift_assignments 
                WHERE id = ? AND tenant_id = ?";
        
        $success = $this->db->query($sql, [$id, Auth::tenantId()])->rowCount() > 0;
        
        if ($success) {
            $this->setFlashMessage('success', 'Asignación eliminada');
        } else {
            $this->setFlashMessage('error', 'Error al eliminar la asignación');
        }
        
        return $this->redirect('/turnos/asignaciones');
    }

    /**
     * Ver asignaciones de un empleado específico
     */
    public function employee(int $userId)
    {
        /** @var User|null $user */
        $user = User::find($userId);
        
        if (!$user || $user->tenant_id != Auth::tenantId()) {
            $this->setFlashMessage('error', 'Empleado no encontrado');
            return $this->redirect('/empleados');
        }
        
        $assignments = $user->shiftAssignments();
        $currentShift = $user->currentShift();
        
        return $this->view('shifts/assignments/employee', [
            'user' => $user,
            'assignments' => $assignments,
            'currentShift' => $currentShift
        ]);
    }

    /**
     * Verificar comprobaciones de fichaje para un empleado
     */
    public function checkClock(int $userId)
    {
        /** @var User|null $user */
        $user = User::find($userId);
        
        if (!$user || $user->tenant_id != Auth::tenantId()) {
            return $this->json(['error' => 'Empleado no encontrado'], 404);
        }
        
        $canClockIn = $user->canClockInNow();
        $canClockOut = $user->canClockOutNow();
        
        return $this->json([
            'employee' => [
                'id' => $user->id,
                'name' => $user->name,
                'has_shift' => $user->hasActiveShift()
            ],
            'clock_in' => $canClockIn,
            'clock_out' => $canClockOut,
            'current_time' => date('H:i:s'),
            'current_date' => date('Y-m-d')
        ]);
    }

    /**
     * Verifica si hay solapamiento de asignaciones
     */
    private function hasOverlappingAssignment(int $userId, string $startDate, ?string $endDate): bool
    {
        $sql = "SELECT COUNT(*) as count 
                FROM shift_assignments 
                WHERE user_id = ? 
                AND is_active = 1
                AND (
                    (start_date <= ? AND (end_date IS NULL OR end_date >= ?))
                    OR (? IS NOT NULL AND start_date <= ? AND (end_date IS NULL OR end_date >= ?))
                )";
        
        $params = [$userId, $startDate, $startDate];
        
        if ($endDate) {
            $params[] = $endDate;
            $params[] = $endDate;
            $params[] = $startDate;
        } else {
            $params[] = null;
            $params[] = null;
            $params[] = null;
        }
        
        $result = $this->db->fetch($sql, $params);
        
        return ($result['count'] ?? 0) > 0;
    }
}
