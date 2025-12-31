<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkCenter;
use App\Models\Project;
use App\Services\ShiftValidationService;
use Core\Database;

/**
 * Controlador de Fichajes
 */
class TimeEntryController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Lista de fichajes y punto de entrada principal
     */
    public function index(): void
    {
        // Diagnóstico opcional del controlador
        if ($this->query('debug_controller')) {
            echo 'OK TimeEntryController@index';
            exit;
        }
        // Guardar acceso directo sin middleware
        if (!Auth::check()) {
            $this->redirect('/login');
            return;
        }
        $userId = Auth::id();
        /** @var User $user */
        $user = User::find($userId);
        
        // Estado actual (detallado para la vista)
        $clockStatus = $user->getClockStatusDetails();
        
        // Fichajes de hoy
        $todayEntries = TimeEntry::getTodayEntries($userId);
        
        // Horas de hoy
        $todayHours = TimeEntry::calculateDailyHours($userId, date('Y-m-d'));
        
        // Proyectos para el select
        $projects = Project::forSelect();
        
        // Turno actual
        $shift = $user->currentShift();

        $this->view('time-entries.index', [
            'title' => 'Fichajes',
            'clockStatus' => $clockStatus,
            'todayEntries' => $todayEntries,
            'todayHours' => $todayHours,
            'projects' => $projects,
            'shift' => $shift,
        ]);
    }

    /**
     * Registra entrada
     */
    public function clockIn(): void
    {
        $userId = Auth::id();
        /** @var User|null $user */
        $user = User::find($userId);
        $currentStatus = $user->getCurrentClockStatus();
        
        // Verificar si ya tiene entrada activa
        if ($currentStatus === 'in') {
            $this->withError('Ya tienes una entrada registrada');
            $this->redirect('/fichajes');
            return;
        }

        // Validar turno asignado
        $shiftValidation = ShiftValidationService::validateClockIn($userId);
        
        // Datos de geolocalización
        $data = [
            'tenant_id' => Auth::tenantId(),
            'latitude' => $this->input('latitude'),
            'longitude' => $this->input('longitude'),
            'accuracy' => $this->input('accuracy'),
            'project_id' => $this->input('project_id') ?: null,
            'work_center_id' => $user->work_center_id,
            'notes' => $this->input('notes'),
            // Información del turno
            'shift_status' => $shiftValidation['status'],
            'shift_id' => $shiftValidation['shift']->id ?? null,
            'expected_time' => $shiftValidation['expected_time'] ?? null,
            'time_difference' => $shiftValidation['difference_minutes'] ?? null,
        ];

        // Validar geolocalización si es requerida
        if ($user->work_center_id) {
            /** @var WorkCenter|null $workCenter */
            $workCenter = WorkCenter::find($user->work_center_id);
            if ($workCenter && $workCenter->requires_geolocation) {
                if (!$data['latitude'] || !$data['longitude']) {
                    $this->withError('Se requiere ubicación para fichar en este centro');
                    $this->redirect('/fichajes');
                    return;
                }
                
                if (!$workCenter->isWithinRadius($data['latitude'], $data['longitude'])) {
                    $distance = $workCenter->calculateDistance($data['latitude'], $data['longitude']);
                    $this->withError("Estás a {$distance}m del centro. Máximo permitido: {$workCenter->radius}m");
                    $this->redirect('/fichajes');
                    return;
                }
            }
        }

        TimeEntry::clockIn($userId, $data);

        // Construir mensaje con información del turno
        $message = 'Entrada registrada a las ' . date('H:i');
        if ($shiftValidation['warning']) {
            $message .= ' ⚠️ ' . $shiftValidation['message'];
            $this->withWarning($message);
        } else {
            $this->withSuccess($message);
        }
        
        $this->redirect('/fichajes');
    }

    /**
     * Registra salida
     */
    public function clockOut(): void
    {
        $userId = Auth::id();
        /** @var User|null $user */
        $user = User::find($userId);
        $currentStatus = $user->getCurrentClockStatus();
        
        // Verificar si tiene entrada activa
        if ($currentStatus !== 'in') {
            $this->withError('No tienes una entrada activa');
            $this->redirect('/fichajes');
            return;
        }

        // Validar turno asignado
        $shiftValidation = ShiftValidationService::validateClockOut($userId);

        $data = [
            'tenant_id' => Auth::tenantId(),
            'latitude' => $this->input('latitude'),
            'longitude' => $this->input('longitude'),
            'accuracy' => $this->input('accuracy'),
            'work_center_id' => $user->work_center_id,
            'notes' => $this->input('notes'),
            // Información del turno
            'shift_status' => $shiftValidation['status'],
            'shift_id' => $shiftValidation['shift']->id ?? null,
            'expected_time' => $shiftValidation['expected_time'] ?? null,
            'time_difference' => $shiftValidation['difference_minutes'] ?? null,
        ];

        TimeEntry::clockOut($userId, $data);

        $hours = TimeEntry::calculateDailyHours($userId, date('Y-m-d'));
        
        // Construir mensaje con información del turno
        $message = "Salida registrada. Total hoy: {$hours}h";
        if ($shiftValidation['warning']) {
            $message .= ' ⚠️ ' . $shiftValidation['message'];
            $this->withWarning($message);
        } else {
            $this->withSuccess($message);
        }
        
        $this->redirect('/fichajes');
    }

    /**
     * Muestra la vista para escanear QR
     */
    public function showScanQr(): void
    {
        $userId = Auth::id();
        /** @var User|null $user */
        $user = User::find($userId);
        $clockStatus = $user->getClockStatusDetails();

        $this->view('time-entries.scan-qr', [
            'title' => 'Escanear QR',
            'clockStatus' => $clockStatus,
        ]);
    }

    /**
     * Test para validar formato QR (modo debug)
     * Usar: POST /fichajes/test-qr con qr_data
     */
    public function testQr(): void
    {
        $qrData = $this->input('qr_data');
        $parts = explode(':', $qrData);
        
        $response = [
            'qr_data' => $qrData,
            'parts' => $parts,
            'is_valid_format' => (count($parts) >= 3 && $parts[0] === 'FICHAJE'),
            'expected_format' => 'FICHAJE:{token}:{tipo}:{workCenterId}',
        ];
        
        if (count($parts) >= 3 && $parts[0] === 'FICHAJE') {
            $token = $parts[1];
            $actionType = $parts[2];
            $workCenterId = $parts[3] ?? null;
            
            $db = Database::getInstance();
            $tenant = $db->fetch("SELECT id, name FROM tenants WHERE qr_token = ?", [$token]);
            
            $response['parsed'] = [
                'token' => $token,
                'action_type' => $actionType,
                'work_center_id' => $workCenterId,
                'tenant_found' => $tenant ? true : false,
                'tenant_name' => $tenant['name'] ?? null,
            ];
        }
        
        $this->json($response);
    }

    /**
     * Procesa el fichaje por QR escaneado
     */
    public function processScanQr(): void
    {
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        /** @var User|null $user */
        $user = User::find($userId);
        
        $qrData = $this->input('qr_data');
        $latitude = $this->input('latitude');
        $longitude = $this->input('longitude');
        
        // Parsear el QR: formato FICHAJE:{tenant_token}:{tipo}
        // tipo = 'in' para entrada, 'out' para salida
        $parts = explode(':', $qrData);
        
        if (count($parts) < 3 || $parts[0] !== 'FICHAJE') {
            $this->json(['success' => false, 'message' => 'Código QR inválido']);
            return;
        }
        
        $qrToken = $parts[1];
        $actionType = $parts[2]; // 'in' o 'out'
        $workCenterId = $parts[3] ?? null;
        
        // Verificar que el token pertenece a la empresa del usuario
        $db = Database::getInstance();
        $tenant = $db->fetch("SELECT * FROM tenants WHERE qr_token = ? AND id = ?", [$qrToken, $tenantId]);
        
        if (!$tenant) {
            $this->json(['success' => false, 'message' => 'Este QR no corresponde a tu empresa']);
            return;
        }
        
        // Verificar estado actual
        $currentStatus = $user->getCurrentClockStatus();
        
        $data = [
            'tenant_id' => $tenantId,
            'latitude' => $latitude ?: null,
            'longitude' => $longitude ?: null,
            'work_center_id' => $workCenterId ?: $user->work_center_id,
            'source' => 'qr_scan',
            'notes' => 'Fichaje mediante escaneo QR',
        ];
        
        try {
            if ($actionType === 'in') {
                if ($currentStatus === 'in') {
                    $this->json(['success' => false, 'message' => 'Ya tienes una entrada registrada. Debes fichar salida primero.']);
                    return;
                }
                TimeEntry::clockIn($userId, $data);
                $this->json([
                    'success' => true,
                    'type' => 'in',
                    'message' => 'Entrada registrada correctamente',
                    'time' => date('H:i:s')
                ]);
            } elseif ($actionType === 'out') {
                if ($currentStatus !== 'in') {
                    $this->json(['success' => false, 'message' => 'No tienes entrada activa. Debes fichar entrada primero.']);
                    return;
                }
                TimeEntry::clockOut($userId, $data);
                $hours = TimeEntry::calculateDailyHours($userId, date('Y-m-d'));
                $this->json([
                    'success' => true,
                    'type' => 'out',
                    'message' => "Salida registrada. Total hoy: {$hours}h",
                    'time' => date('H:i:s')
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Tipo de fichaje inválido']);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }

    /**
     * Procesa el fichaje solo con ubicación (sin QR)
     */
    public function processLocationClock(): void
    {
        $userId = Auth::id();
        $tenantId = Auth::tenantId();
        /** @var User|null $user */
        $user = User::find($userId);
        
        $actionType = $this->input('action_type'); // 'in' o 'out'
        $latitude = $this->input('latitude');
        $longitude = $this->input('longitude');
        
        if (!in_array($actionType, ['in', 'out'])) {
            $this->json(['success' => false, 'message' => 'Tipo de fichaje inválido']);
            return;
        }
        
        // Verificar estado actual
        $currentStatus = $user->getCurrentClockStatus();
        
        // Validar geolocalización si el centro de trabajo lo requiere
        if ($user->work_center_id && $latitude && $longitude) {
            /** @var WorkCenter|null $workCenter */
            $workCenter = WorkCenter::find($user->work_center_id);
            if ($workCenter && $workCenter->requires_geolocation) {
                if (!$workCenter->isWithinRadius($latitude, $longitude)) {
                    $distance = $workCenter->calculateDistance($latitude, $longitude);
                    $this->json([
                        'success' => false, 
                        'message' => "Estás a {$distance}m del centro. Máximo permitido: {$workCenter->radius}m"
                    ]);
                    return;
                }
            }
        }
        
        $data = [
            'tenant_id' => $tenantId,
            'latitude' => $latitude ?: null,
            'longitude' => $longitude ?: null,
            'work_center_id' => $user->work_center_id,
            'source' => 'location',
            'notes' => 'Fichaje mediante ubicación GPS',
        ];
        
        try {
            if ($actionType === 'in') {
                if ($currentStatus === 'in') {
                    $this->json(['success' => false, 'message' => 'Ya tienes una entrada registrada. Debes fichar salida primero.']);
                    return;
                }
                TimeEntry::clockIn($userId, $data);
                $this->json([
                    'success' => true,
                    'type' => 'in',
                    'message' => 'Entrada registrada correctamente',
                    'time' => date('H:i:s')
                ]);
            } else {
                if ($currentStatus !== 'in') {
                    $this->json(['success' => false, 'message' => 'No tienes entrada activa. Debes fichar entrada primero.']);
                    return;
                }
                TimeEntry::clockOut($userId, $data);
                $hours = TimeEntry::calculateDailyHours($userId, date('Y-m-d'));
                $this->json([
                    'success' => true,
                    'type' => 'out',
                    'message' => "Salida registrada. Total hoy: {$hours}h",
                    'time' => date('H:i:s')
                ]);
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }

    /**
     * Historial de fichajes
     */
    public function history(): void
    {
        $userId = Auth::id();
        $db = Database::getInstance();
        
        // Filtros
        $startDate = $this->query('start_date', date('Y-m-01'));
        $endDate = $this->query('end_date', date('Y-m-t'));
        
        // Obtener fichajes del período
        $sql = "SELECT te.*, wc.name as work_center_name, p.name as project_name
                FROM time_entries te
                LEFT JOIN work_centers wc ON te.work_center_id = wc.id
                LEFT JOIN projects p ON te.project_id = p.id
                WHERE te.user_id = ? 
                AND DATE(te.recorded_at) BETWEEN ? AND ?
                ORDER BY te.recorded_at DESC";
        
        $entries = $db->fetchAll($sql, [$userId, $startDate, $endDate]);
        
        // Agrupar por día
        $groupedEntries = [];
        foreach ($entries as $entry) {
            $date = date('Y-m-d', strtotime($entry['recorded_at']));
            if (!isset($groupedEntries[$date])) {
                $groupedEntries[$date] = [
                    'entries' => [],
                    'hours' => 0,
                ];
            }
            $groupedEntries[$date]['entries'][] = $entry;
        }
        
        // Calcular horas por día
        foreach (array_keys($groupedEntries) as $date) {
            $groupedEntries[$date]['hours'] = TimeEntry::calculateDailyHours($userId, $date);
        }

        $this->view('time-entries.history', [
            'title' => 'Historial de Fichajes',
            'groupedEntries' => $groupedEntries,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Ver detalle de fichaje
     */
    public function show(int $id): void
    {
        /** @var TimeEntry|null $entry */
        $entry = TimeEntry::find($id);
        
        if (!$entry) {
            $this->withError('Fichaje no encontrado');
            $this->redirect('/fichajes/historial');
            return;
        }

        $this->view('time-entries.show', [
            'title' => 'Detalle de Fichaje',
            'entry' => $entry,
            'user' => $entry->user(),
            'workCenter' => $entry->workCenter(),
            'project' => $entry->project(),
        ]);
    }

    /**
     * Aprobar fichaje (para admins)
     */
    public function approve(int $id): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para esta acción');
            $this->redirect('/fichajes');
            return;
        }

        $entry = TimeEntry::find($id);
        
        if (!$entry) {
            $this->withError('Fichaje no encontrado');
            $this->redirect('/fichajes');
            return;
        }

        $entry->status = 'approved';
        $entry->approved_by = Auth::id();
        $entry->approved_at = date('Y-m-d H:i:s');
        $entry->save();

        $this->withSuccess('Fichaje aprobado correctamente');
        $this->redirect('/fichajes');
    }

    /**
     * Rechazar fichaje
     */
    public function reject(int $id): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para esta acción');
            $this->redirect('/fichajes');
            return;
        }

        $entry = TimeEntry::find($id);
        
        if (!$entry) {
            $this->withError('Fichaje no encontrado');
            $this->redirect('/fichajes');
            return;
        }

        $entry->status = 'rejected';
        $entry->approved_by = Auth::id();
        $entry->approved_at = date('Y-m-d H:i:s');
        $entry->rejection_reason = $this->input('reason');
        $entry->save();

        $this->withSuccess('Fichaje rechazado');
        $this->redirect('/fichajes');
    }
}
