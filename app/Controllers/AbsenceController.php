<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\Holiday;
use App\Models\User;
use Core\Database;

/**
 * Controlador de Ausencias
 */
class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Lista de ausencias
     */
    public function index(): void
    {
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        $db = Database::getInstance();
        
        // Filtros
        $status = $this->query('status', '');
        $type = $this->query('type', '');
        $view = $this->query('view', 'own'); // own, team, all
        
        // Base query
        $sql = "SELECT a.*, u.name as user_name, at.name as type_name, at.color,
                       ap.name as approved_by_name
                FROM absences a
                JOIN users u ON a.user_id = u.id
                JOIN absence_types at ON a.absence_type_id = at.id
                LEFT JOIN users ap ON a.approved_by = ap.id
                WHERE a.tenant_id = ? AND a.deleted_at IS NULL";
        
        $params = [$tenantId];
        
        // Filtro por vista
        if ($view === 'own' || !Auth::isSupervisor()) {
            $sql .= " AND a.user_id = ?";
            $params[] = $userId;
        }
        
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }
        
        if ($type) {
            $sql .= " AND a.absence_type_id = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY a.start_date DESC";
        
        $absences = $db->fetchAll($sql, $params);
        $absenceTypes = AbsenceType::forSelect($tenantId);
        
        // Balance de vacaciones del usuario
        $holidayBalance = Holiday::getOrCreate($userId);
        
        // Estadísticas
        $currentYear = date('Y');
        $statistics = [
            'available_vacation_days' => $holidayBalance->getRemaining(),
            'used_vacation_days' => $holidayBalance->used_days ?? 0,
            'total_absences' => $db->fetch(
                "SELECT COUNT(*) as count FROM absences WHERE user_id = ? AND tenant_id = ? AND YEAR(start_date) = ? AND deleted_at IS NULL",
                [$userId, $tenantId, $currentYear]
            )['count'] ?? 0,
            'pending_approvals' => $db->fetch(
                "SELECT COUNT(*) as count FROM absences WHERE user_id = ? AND tenant_id = ? AND status = 'pending' AND deleted_at IS NULL",
                [$userId, $tenantId]
            )['count'] ?? 0,
        ];

        $this->view('absences.index', [
            'title' => 'Ausencias',
            'absences' => $absences,
            'absenceTypes' => $absenceTypes,
            'holidayBalance' => $holidayBalance->toArray(),
            'statistics' => $statistics,
            'filters' => [
                'status' => $status,
                'type' => $type,
                'view' => $view,
            ],
        ]);
    }

    /**
     * Formulario de solicitud
     */
    public function create(): void
    {
        $tenantId = Auth::tenantId();
        $userId = Auth::id();
        
        $absenceTypes = AbsenceType::forTenant($tenantId);
        $holidayBalance = Holiday::getOrCreate($userId);

        $this->view('absences.create', [
            'title' => 'Solicitar Ausencia',
            'absenceTypes' => $absenceTypes,
            'holidayBalance' => $holidayBalance,
        ]);
    }

    /**
     * Guarda la solicitud
     */
    public function store(): void
    {
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        $data = $this->input();
        
        // Validar
        $errors = $this->validate($data, [
            'absence_type_id' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        if (!empty($errors)) {
            $this->withErrors($errors)->withOld($data);
            $this->redirect('/ausencias/solicitar');
            return;
        }

        // Verificar fechas
        if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            $this->withError('La fecha fin no puede ser anterior a la fecha inicio')
                 ->withOld($data);
            $this->redirect('/ausencias/solicitar');
            return;
        }

        // Verificar solapamiento
        if (Absence::hasOverlap($userId, $data['start_date'], $data['end_date'])) {
            $this->withError('Ya tienes una ausencia registrada en esas fechas')
                 ->withOld($data);
            $this->redirect('/ausencias/solicitar');
            return;
        }

        // Calcular días
        $start = new \DateTime($data['start_date']);
        $end = new \DateTime($data['end_date']);
        $diff = $start->diff($end);
        $totalDays = $diff->days + 1;

        // Obtener tipo de ausencia
        $absenceType = AbsenceType::find($data['absence_type_id']);
        $status = $absenceType->requires_approval ? 'pending' : 'approved';

        // Crear ausencia
        $absence = Absence::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'absence_type_id' => $data['absence_type_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_days' => $totalDays,
            'reason' => $data['reason'] ?? null,
            'status' => $status,
        ]);

        // Si es vacaciones, actualizar balance
        if ($absenceType->code === 'VAC') {
            $holiday = Holiday::getOrCreate($userId);
            $holiday->addPendingDays($totalDays);
        }

        $message = $status === 'pending' 
            ? 'Solicitud enviada. Pendiente de aprobación.'
            : 'Ausencia registrada correctamente.';
        
        $this->withSuccess($message);
        $this->redirect('/ausencias');
    }

    /**
     * Ver detalle
     */
    public function show(int $id): void
    {
        $absence = Absence::find($id);
        
        if (!$absence) {
            $this->withError('Ausencia no encontrada');
            $this->redirect('/ausencias');
            return;
        }

        $this->view('absences.show', [
            'title' => 'Detalle de Ausencia',
            'absence' => $absence,
            'user' => $absence->user(),
            'absenceType' => $absence->absenceType(),
            'approvedBy' => $absence->approvedBy(),
        ]);
    }

    /**
     * Aprueba ausencia
     */
    public function approve(int $id): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para aprobar ausencias');
            $this->redirect('/ausencias');
            return;
        }

        $absence = Absence::find($id);
        
        if (!$absence) {
            $this->withError('Ausencia no encontrada');
            $this->redirect('/ausencias');
            return;
        }

        $absence->approve(Auth::id());

        // Si es vacaciones, confirmar días pendientes
        $absenceType = $absence->absenceType();
        if ($absenceType && $absenceType->code === 'VAC') {
            $holiday = Holiday::getOrCreate($absence->user_id);
            $holiday->confirmPending($absence->total_days);
        }

        $this->withSuccess('Ausencia aprobada correctamente');
        $this->redirect('/ausencias');
    }

    /**
     * Rechaza ausencia
     */
    public function reject(int $id): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para rechazar ausencias');
            $this->redirect('/ausencias');
            return;
        }

        $absence = Absence::find($id);
        
        if (!$absence) {
            $this->withError('Ausencia no encontrada');
            $this->redirect('/ausencias');
            return;
        }

        $reason = $this->input('reason');
        $absence->reject(Auth::id(), $reason);

        // Si es vacaciones, liberar días pendientes
        $absenceType = $absence->absenceType();
        if ($absenceType && $absenceType->code === 'VAC') {
            $holiday = Holiday::getOrCreate($absence->user_id);
            $holiday->releasePending($absence->total_days);
        }

        $this->withSuccess('Ausencia rechazada');
        $this->redirect('/ausencias');
    }

    /**
     * Cancela ausencia (por el propio usuario)
     */
    public function cancel(int $id): void
    {
        $absence = Absence::find($id);
        
        if (!$absence) {
            $this->withError('Ausencia no encontrada');
            $this->redirect('/ausencias');
            return;
        }

        // Solo puede cancelar el propio usuario o un admin
        if ($absence->user_id !== Auth::id() && !Auth::isAdmin()) {
            $this->withError('No tienes permisos para cancelar esta ausencia');
            $this->redirect('/ausencias');
            return;
        }

        // Solo cancelar si está pendiente
        if ($absence->status !== 'pending') {
            $this->withError('Solo se pueden cancelar solicitudes pendientes');
            $this->redirect('/ausencias');
            return;
        }

        $absence->cancel();

        // Si es vacaciones, liberar días
        $absenceType = $absence->absenceType();
        if ($absenceType && $absenceType->code === 'VAC') {
            $holiday = Holiday::getOrCreate($absence->user_id);
            $holiday->releasePending($absence->total_days);
        }

        $this->withSuccess('Solicitud cancelada');
        $this->redirect('/ausencias');
    }
}
