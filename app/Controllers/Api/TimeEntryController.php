<?php
namespace App\Controllers\Api;

use App\Models\TimeEntry;
use App\Models\User;
use App\Models\WorkCenter;
use App\Services\ShiftValidationService;
use Core\Database;

/**
 * Controlador API para Fichajes
 */
class TimeEntryController extends ApiController
{
    private ShiftValidationService $shiftValidator;
    
    public function __construct()
    {
        $this->shiftValidator = new ShiftValidationService();
    }
    
    /**
     * Obtener estado actual del fichaje
     * GET /api/v1/time-entries/status
     */
    public function status(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        $clockStatus = $this->getClockStatus($user);
        
        $this->success([
            'clock_status' => $clockStatus,
            'current_shift' => $this->getCurrentShiftInfo($user),
            'today_entries' => $this->getTodayEntries($user),
            'today_summary' => $this->getTodaySummary($user),
        ]);
    }
    
    /**
     * Fichar entrada
     * POST /api/v1/time-entries/clock-in
     * 
     * Body: { "method": "qr|geolocation|manual", "qr_token": "...", "latitude": ..., "longitude": ..., "work_center_id": ... }
     */
    public function clockIn(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        // Verificar si ya fichó entrada
        $currentStatus = $this->getClockStatus($user);
        
        if ($currentStatus['is_clocked_in']) {
            $this->error('Ya has fichado entrada hoy. Debes fichar salida primero.', 400);
        }
        
        $method = $this->input('method') ?? 'manual';
        $workCenterId = null;
        $locationData = null;
        
        // Validar según método
        switch ($method) {
            case 'qr':
                $validation = $this->validateQrClockIn();
                if (!$validation['valid']) {
                    $this->error($validation['error'], 400);
                }
                $workCenterId = $validation['work_center_id'];
                break;
                
            case 'geolocation':
                $validation = $this->validateGeolocationClockIn($user);
                if (!$validation['valid']) {
                    $this->error($validation['error'], 400);
                }
                $workCenterId = $validation['work_center_id'];
                $locationData = $validation['location'];
                break;
                
            case 'manual':
                // Usar el centro de trabajo del usuario
                $workCenterId = $user->work_center_id;
                break;
                
            default:
                $this->error('Método de fichaje no válido', 400);
        }
        
        // Validar turno
        $shiftValidation = $this->shiftValidator->validateClockIn($user->id);
        
        // Crear entrada
        $now = date('Y-m-d H:i:s');
        $db = Database::getInstance();
        
        $entryId = $db->insert('time_entries', [
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'recorded_at' => $now,
            'type' => 'clock_in',
            'work_center_id' => $workCenterId,
            'source' => $method === 'manual' ? 'mobile' : $method,
            'latitude' => $locationData['latitude'] ?? null,
            'longitude' => $locationData['longitude'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'device_info' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'status' => 'approved',
            'metadata' => json_encode([
                'shift_id' => $shiftValidation['shift_id'] ?? null,
                'shift_validation_status' => $shiftValidation['status'] ?? 'unassigned',
                'shift_validation_message' => $shiftValidation['message'] ?? null,
                'early_minutes' => $shiftValidation['early_minutes'] ?? null,
                'late_minutes' => $shiftValidation['late_minutes'] ?? null,
                'clock_method' => $method,
            ]),
            'created_at' => $now,
        ]);
        
        // Respuesta
        $entry = TimeEntry::find($entryId);
        
        $response = [
            'entry' => [
                'id' => $entry->id,
                'type' => 'clock_in',
                'recorded_at' => $entry->recorded_at,
                'method' => $method,
                'work_center' => $workCenterId ? WorkCenter::find($workCenterId)?->name : null,
            ],
            'clock_status' => $this->getClockStatus($user),
            'shift_validation' => $shiftValidation,
        ];
        
        // Agregar advertencia si llegó tarde o temprano
        $message = 'Entrada registrada correctamente';
        if (!empty($shiftValidation['warning'])) {
            $message .= '. ' . $shiftValidation['warning'];
        }
        
        $this->success($response, $message);
    }
    
    /**
     * Fichar salida
     * POST /api/v1/time-entries/clock-out
     */
    public function clockOut(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        // Verificar si tiene entrada activa
        $currentStatus = $this->getClockStatus($user);
        
        if (!$currentStatus['is_clocked_in']) {
            $this->error('No tienes una entrada activa. Debes fichar entrada primero.', 400);
        }
        
        $method = $this->input('method') ?? 'manual';
        $locationData = null;
        
        // Validar geolocalización si aplica
        if ($method === 'geolocation') {
            $validation = $this->validateGeolocationClockIn($user);
            if (!$validation['valid']) {
                $this->error($validation['error'], 400);
            }
            $locationData = $validation['location'];
        }
        
        // Validar turno
        $shiftValidation = $this->shiftValidator->validateClockOut($user->id);
        
        // Crear salida
        $now = date('Y-m-d H:i:s');
        $db = Database::getInstance();
        
        $entryId = $db->insert('time_entries', [
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'recorded_at' => $now,
            'type' => 'clock_out',
            'work_center_id' => $user->work_center_id,
            'source' => $method === 'manual' ? 'mobile' : $method,
            'latitude' => $locationData['latitude'] ?? null,
            'longitude' => $locationData['longitude'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'device_info' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'status' => 'approved',
            'metadata' => json_encode([
                'shift_id' => $shiftValidation['shift_id'] ?? null,
                'shift_validation_status' => $shiftValidation['status'] ?? 'unassigned',
                'shift_validation_message' => $shiftValidation['message'] ?? null,
                'early_minutes' => $shiftValidation['early_minutes'] ?? null,
                'overtime_minutes' => $shiftValidation['overtime_minutes'] ?? null,
                'clock_method' => $method,
            ]),
            'created_at' => $now,
        ]);
        
        // Calcular horas trabajadas hoy
        $todaySummary = $this->getTodaySummary($user);
        
        $response = [
            'entry' => [
                'id' => $entryId,
                'type' => 'clock_out',
                'recorded_at' => $now,
                'method' => $method,
            ],
            'clock_status' => $this->getClockStatus($user),
            'shift_validation' => $shiftValidation,
            'today_summary' => $todaySummary,
        ];
        
        $message = 'Salida registrada correctamente';
        if (!empty($shiftValidation['warning'])) {
            $message .= '. ' . $shiftValidation['warning'];
        }
        
        $this->success($response, $message);
    }
    
    /**
     * Fichar con código QR
     * POST /api/v1/time-entries/qr
     * 
     * Body: { "qr_token": "...", "action": "auto|clock_in|clock_out" }
     */
    public function qr(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        $qrToken = $this->input('qr_token');
        
        if (!$qrToken) {
            $this->error('Código QR requerido', 400);
        }
        
        // Validar QR
        $validation = $this->validateQrToken($qrToken);
        
        if (!$validation['valid']) {
            $this->error($validation['error'], 400);
        }
        
        // Determinar acción automáticamente o por parámetro
        $action = $this->input('action') ?? 'auto';
        $currentStatus = $this->getClockStatus($user);
        
        if ($action === 'auto') {
            $action = $currentStatus['is_clocked_in'] ? 'clock_out' : 'clock_in';
        }
        
        // Ejecutar fichaje
        $_POST['method'] = 'qr';
        $_POST['qr_token'] = $qrToken;
        
        if ($action === 'clock_in') {
            $this->clockIn();
        } else {
            $this->clockOut();
        }
    }
    
    /**
     * Historial de fichajes
     * GET /api/v1/time-entries/history
     * 
     * Query: ?from=2024-01-01&to=2024-01-31&page=1&per_page=20
     */
    public function history(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        $from = $_GET['from'] ?? date('Y-m-01'); // Primer día del mes actual
        $to = $_GET['to'] ?? date('Y-m-d');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;
        
        $db = Database::getInstance();
        
        // Obtener total
        $total = $db->fetch(
            "SELECT COUNT(*) as count FROM time_entries 
             WHERE user_id = ? AND DATE(recorded_at) BETWEEN ? AND ?",
            [$user->id, $from, $to]
        )['count'];
        
        // Obtener registros
        $entries = $db->fetchAll(
            "SELECT te.*, wc.name as work_center_name
             FROM time_entries te
             LEFT JOIN work_centers wc ON te.work_center_id = wc.id
             WHERE te.user_id = ? AND DATE(te.recorded_at) BETWEEN ? AND ?
             ORDER BY te.recorded_at DESC
             LIMIT ? OFFSET ?",
            [$user->id, $from, $to, $perPage, $offset]
        );
        
        // Formatear respuesta
        $formatted = array_map(function($entry) {
            // Obtener datos de validación del metadata
            $metadata = json_decode($entry['metadata'] ?? '{}', true) ?: [];
            
            return [
                'id' => $entry['id'],
                'type' => $entry['type'],
                'recorded_at' => $entry['recorded_at'],
                'date' => date('Y-m-d', strtotime($entry['recorded_at'])),
                'time' => date('H:i:s', strtotime($entry['recorded_at'])),
                'method' => $metadata['clock_method'] ?? $entry['source'] ?? 'manual',
                'work_center' => $entry['work_center_name'],
                'validation_status' => $metadata['shift_validation_status'] ?? null,
                'validation_message' => $metadata['shift_validation_message'] ?? null,
                'late_minutes' => $metadata['late_minutes'] ?? null,
                'early_minutes' => $metadata['early_minutes'] ?? null,
                'overtime_minutes' => $metadata['overtime_minutes'] ?? null,
            ];
        }, $entries);
        
        $this->success([
            'entries' => $formatted,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => (int) $total,
                'total_pages' => ceil($total / $perPage),
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }
    
    /**
     * Resumen de horas
     * GET /api/v1/time-entries/summary
     * 
     * Query: ?period=week|month|custom&from=2024-01-01&to=2024-01-31
     */
    public function summary(): void
    {
        $user = $this->user();
        
        if (!$user) {
            $this->error('No autenticado', 401);
        }
        
        $period = $_GET['period'] ?? 'week';
        
        switch ($period) {
            case 'week':
                $from = date('Y-m-d', strtotime('monday this week'));
                $to = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'month':
                $from = date('Y-m-01');
                $to = date('Y-m-t');
                break;
            case 'custom':
                $from = $_GET['from'] ?? date('Y-m-01');
                $to = $_GET['to'] ?? date('Y-m-d');
                break;
            default:
                $from = date('Y-m-01');
                $to = date('Y-m-d');
        }
        
        $db = Database::getInstance();
        
        // Calcular horas trabajadas por día
        $dailyHours = $db->fetchAll(
            "SELECT 
                DATE(recorded_at) as date,
                type,
                recorded_at
             FROM time_entries 
             WHERE user_id = ? AND DATE(recorded_at) BETWEEN ? AND ?
             ORDER BY recorded_at ASC",
            [$user->id, $from, $to]
        );
        
        // Agrupar por día y calcular horas
        $summary = $this->calculateHoursSummary($dailyHours, $from, $to);
        
        $this->success([
            'period' => [
                'type' => $period,
                'from' => $from,
                'to' => $to,
            ],
            'summary' => $summary,
        ]);
    }
    
    // ========== Métodos Auxiliares ==========
    
    /**
     * Obtiene el estado actual del fichaje
     */
    private function getClockStatus(User $user): array
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        // Obtener última entrada/salida de hoy
        $lastEntry = $db->fetch(
            "SELECT * FROM time_entries 
             WHERE user_id = ? AND DATE(recorded_at) = ?
             ORDER BY recorded_at DESC LIMIT 1",
            [$user->id, $today]
        );
        
        $isClockedIn = $lastEntry && $lastEntry['type'] === 'clock_in';
        
        // Tiempo trabajado hoy
        $todayEntries = $db->fetchAll(
            "SELECT type, recorded_at FROM time_entries 
             WHERE user_id = ? AND DATE(recorded_at) = ?
             ORDER BY recorded_at ASC",
            [$user->id, $today]
        );
        
        $workedMinutes = $this->calculateWorkedMinutes($todayEntries, $isClockedIn);
        
        // Obtener método del metadata
        $metadata = $lastEntry ? json_decode($lastEntry['metadata'] ?? '{}', true) : [];
        
        return [
            'is_clocked_in' => $isClockedIn,
            'last_action' => $lastEntry ? [
                'type' => $lastEntry['type'],
                'time' => $lastEntry['recorded_at'],
                'method' => $metadata['clock_method'] ?? $lastEntry['source'] ?? 'manual',
            ] : null,
            'worked_today' => [
                'minutes' => $workedMinutes,
                'formatted' => sprintf('%02d:%02d', floor($workedMinutes / 60), $workedMinutes % 60),
            ],
            'current_session_start' => $isClockedIn ? $lastEntry['recorded_at'] : null,
        ];
    }
    
    /**
     * Calcula minutos trabajados
     */
    private function calculateWorkedMinutes(array $entries, bool $includeCurrent = false): int
    {
        $totalMinutes = 0;
        $clockInTime = null;
        
        foreach ($entries as $entry) {
            if ($entry['type'] === 'clock_in') {
                $clockInTime = strtotime($entry['recorded_at']);
            } elseif ($entry['type'] === 'clock_out' && $clockInTime) {
                $clockOutTime = strtotime($entry['recorded_at']);
                $totalMinutes += ($clockOutTime - $clockInTime) / 60;
                $clockInTime = null;
            }
        }
        
        // Si está fichado actualmente, agregar tiempo hasta ahora
        if ($includeCurrent && $clockInTime) {
            $totalMinutes += (time() - $clockInTime) / 60;
        }
        
        return (int) $totalMinutes;
    }
    
    /**
     * Obtiene info del turno actual
     */
    private function getCurrentShiftInfo(User $user): ?array
    {
        $shift = $user->currentShift();
        
        if (!$shift) {
            return null;
        }
        
        return [
            'id' => $shift->id,
            'name' => $shift->name,
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'working_days' => $shift->getWorkingDays(),
            'is_work_day' => $this->isShiftWorkDay($shift, date('N')),
        ];
    }
    
    /**
     * Obtiene las entradas de hoy
     */
    private function getTodayEntries(User $user): array
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        $entries = $db->fetchAll(
            "SELECT id, type, recorded_at, source, metadata
             FROM time_entries 
             WHERE user_id = ? AND DATE(recorded_at) = ?
             ORDER BY recorded_at ASC",
            [$user->id, $today]
        );
        
        return array_map(function($e) {
            $metadata = json_decode($e['metadata'] ?? '{}', true) ?: [];
            return [
                'id' => $e['id'],
                'type' => $e['type'],
                'time' => date('H:i:s', strtotime($e['recorded_at'])),
                'method' => $metadata['clock_method'] ?? $e['source'] ?? 'manual',
                'status' => $metadata['shift_validation_status'] ?? null,
            ];
        }, $entries);
    }
    
    /**
     * Resumen de hoy
     */
    private function getTodaySummary(User $user): array
    {
        $db = Database::getInstance();
        $today = date('Y-m-d');
        
        $entries = $db->fetchAll(
            "SELECT type, recorded_at FROM time_entries 
             WHERE user_id = ? AND DATE(recorded_at) = ?
             ORDER BY recorded_at ASC",
            [$user->id, $today]
        );
        
        $clockStatus = $this->getClockStatus($user);
        $workedMinutes = $this->calculateWorkedMinutes($entries, $clockStatus['is_clocked_in']);
        
        // Horas esperadas del turno
        $shift = $user->currentShift();
        $expectedMinutes = 0;
        
        if ($shift && $this->isShiftWorkDay($shift, date('N'))) {
            $start = strtotime($shift->start_time);
            $end = strtotime($shift->end_time);
            $expectedMinutes = ($end - $start) / 60;
            if ($expectedMinutes < 0) {
                $expectedMinutes += 24 * 60; // Turno nocturno
            }
        }
        
        $difference = $workedMinutes - $expectedMinutes;
        
        return [
            'date' => $today,
            'worked_minutes' => $workedMinutes,
            'worked_formatted' => sprintf('%02d:%02d', floor($workedMinutes / 60), $workedMinutes % 60),
            'expected_minutes' => $expectedMinutes,
            'expected_formatted' => sprintf('%02d:%02d', floor($expectedMinutes / 60), $expectedMinutes % 60),
            'difference_minutes' => $difference,
            'difference_formatted' => ($difference >= 0 ? '+' : '-') . sprintf('%02d:%02d', floor(abs($difference) / 60), abs($difference) % 60),
            'entries_count' => count($entries),
        ];
    }
    
    /**
     * Valida fichaje con QR
     */
    private function validateQrClockIn(): array
    {
        $qrToken = $this->input('qr_token');
        
        if (!$qrToken) {
            return ['valid' => false, 'error' => 'Código QR requerido'];
        }
        
        return $this->validateQrToken($qrToken);
    }
    
    /**
     * Valida un token QR
     */
    private function validateQrToken(string $token): array
    {
        $db = Database::getInstance();
        
        // Buscar centro de trabajo con ese token
        $workCenter = $db->fetch(
            "SELECT * FROM work_centers WHERE qr_token = ? AND is_active = 1",
            [$token]
        );
        
        if (!$workCenter) {
            return ['valid' => false, 'error' => 'Código QR inválido o centro de trabajo inactivo'];
        }
        
        // Verificar tenant del usuario
        $user = $this->user();
        if ($workCenter['tenant_id'] !== $user->tenant_id) {
            return ['valid' => false, 'error' => 'Este código QR no pertenece a tu empresa'];
        }
        
        return [
            'valid' => true,
            'work_center_id' => $workCenter['id'],
            'work_center_name' => $workCenter['name'],
        ];
    }
    
    /**
     * Valida fichaje con geolocalización
     */
    private function validateGeolocationClockIn(User $user): array
    {
        $latitude = $this->input('latitude');
        $longitude = $this->input('longitude');
        
        if (!$latitude || !$longitude) {
            return ['valid' => false, 'error' => 'Ubicación requerida'];
        }
        
        $db = Database::getInstance();
        
        // Buscar centros de trabajo del tenant
        $workCenters = $db->fetchAll(
            "SELECT * FROM work_centers WHERE tenant_id = ? AND is_active = 1",
            [$user->tenant_id]
        );
        
        // Encontrar el centro más cercano dentro del radio permitido
        $nearestCenter = null;
        $minDistance = PHP_FLOAT_MAX;
        
        foreach ($workCenters as $wc) {
            if (!$wc['latitude'] || !$wc['longitude']) {
                continue;
            }
            
            $distance = $this->calculateDistance(
                $latitude, $longitude,
                $wc['latitude'], $wc['longitude']
            );
            
            $allowedRadius = $wc['radius'] ?? 100; // metros
            
            if ($distance <= $allowedRadius && $distance < $minDistance) {
                $minDistance = $distance;
                $nearestCenter = $wc;
            }
        }
        
        if (!$nearestCenter) {
            return [
                'valid' => false, 
                'error' => 'No estás dentro del rango de ningún centro de trabajo'
            ];
        }
        
        return [
            'valid' => true,
            'work_center_id' => $nearestCenter['id'],
            'work_center_name' => $nearestCenter['name'],
            'distance' => round($minDistance),
            'location' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
        ];
    }
    
    /**
     * Calcula distancia entre dos puntos GPS (Haversine)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // metros
        
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);
        
        $a = sin($deltaLat / 2) ** 2 +
             cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Verifica si un día es día de trabajo para un turno
     */
    private function isShiftWorkDay($shift, string $dayNumber): bool
    {
        $workingDays = $shift->getWorkingDays();
        return in_array((int) $dayNumber, $workingDays, true);
    }
    
    /**
     * Calcula resumen de horas para un período
     */
    private function calculateHoursSummary(array $entries, string $from, string $to): array
    {
        $dailyData = [];
        $totalMinutes = 0;
        $daysWorked = 0;
        
        // Agrupar entradas por día
        $byDay = [];
        foreach ($entries as $entry) {
            $date = $entry['date'];
            if (!isset($byDay[$date])) {
                $byDay[$date] = [];
            }
            $byDay[$date][] = $entry;
        }
        
        // Calcular horas por día
        foreach ($byDay as $date => $dayEntries) {
            $dayMinutes = $this->calculateWorkedMinutes($dayEntries, false);
            
            if ($dayMinutes > 0) {
                $daysWorked++;
                $totalMinutes += $dayMinutes;
                
                $dailyData[] = [
                    'date' => $date,
                    'minutes' => $dayMinutes,
                    'formatted' => sprintf('%02d:%02d', floor($dayMinutes / 60), $dayMinutes % 60),
                    'entries' => count($dayEntries),
                ];
            }
        }
        
        $avgMinutes = $daysWorked > 0 ? $totalMinutes / $daysWorked : 0;
        
        return [
            'total_minutes' => $totalMinutes,
            'total_formatted' => sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60),
            'days_worked' => $daysWorked,
            'average_minutes_per_day' => round($avgMinutes),
            'average_formatted' => sprintf('%02d:%02d', floor($avgMinutes / 60), $avgMinutes % 60),
            'daily' => $dailyData,
        ];
    }
}
