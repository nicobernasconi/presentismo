<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Modelo TimeEntry (Fichaje)
 */
class TimeEntry extends Model
{
    protected static string $table = 'time_entries';
    protected static bool $timestamps = true;
    
    protected static array $fillable = [
        'tenant_id',
        'user_id',
        'work_center_id',
        'project_id',
        'task_id',
        'type',
        'recorded_at',
        'adjusted_at',
        'latitude',
        'longitude',
        'accuracy',
        'location_address',
        'source',
        'device_info',
        'ip_address',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
        'metadata',
    ];

    const TYPE_CLOCK_IN = 'clock_in';
    const TYPE_CLOCK_OUT = 'clock_out';
    const TYPE_BREAK_START = 'break_start';
    const TYPE_BREAK_END = 'break_end';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Obtiene el usuario
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Obtiene el centro de trabajo
     */
    public function workCenter(): ?WorkCenter
    {
        if (!$this->work_center_id) return null;
        return WorkCenter::find($this->work_center_id);
    }

    /**
     * Obtiene el proyecto
     */
    public function project(): ?Project
    {
        if (!$this->project_id) return null;
        return Project::find($this->project_id);
    }

    /**
     * Etiqueta del tipo
     */
    public function getTypeLabel(): string
    {
        switch ($this->type) {
            case 'clock_in': return 'Entrada';
            case 'clock_out': return 'Salida';
            case 'break_start': return 'Inicio Descanso';
            case 'break_end': return 'Fin Descanso';
            default: return $this->type;
        }
    }

    /**
     * Color del tipo para badges
     */
    public function getTypeColor(): string
    {
        switch ($this->type) {
            case 'clock_in': return 'green';
            case 'clock_out': return 'red';
            case 'break_start': return 'yellow';
            case 'break_end': return 'blue';
            default: return 'gray';
        }
    }

    /**
     * Etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case 'pending': return 'Pendiente';
            case 'approved': return 'Aprobado';
            case 'rejected': return 'Rechazado';
            default: return $this->status;
        }
    }

    /**
     * Color del estado
     */
    public function getStatusColor(): string
    {
        switch ($this->status) {
            case 'pending': return 'yellow';
            case 'approved': return 'green';
            case 'rejected': return 'red';
            default: return 'gray';
        }
    }

    /**
     * Hora formateada
     */
    public function getFormattedTime(): string
    {
        $time = $this->adjusted_at ?? $this->recorded_at;
        return date('H:i', strtotime($time));
    }

    /**
     * Fecha formateada
     */
    public function getFormattedDate(): string
    {
        return date('d/m/Y', strtotime($this->recorded_at));
    }

    /**
     * Registra entrada
     */
    public static function clockIn(int $userId, array $data = []): self
    {
        return self::create(array_merge([
            'user_id' => $userId,
            'type' => self::TYPE_CLOCK_IN,
            'recorded_at' => date('Y-m-d H:i:s'),
            'source' => 'web',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'status' => self::STATUS_APPROVED,
        ], $data));
    }

    /**
     * Registra salida
     */
    public static function clockOut(int $userId, array $data = []): self
    {
        return self::create(array_merge([
            'user_id' => $userId,
            'type' => self::TYPE_CLOCK_OUT,
            'recorded_at' => date('Y-m-d H:i:s'),
            'source' => 'web',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'status' => self::STATUS_APPROVED,
        ], $data));
    }

    /**
     * Obtiene fichajes del día para un usuario
     */
    public static function getTodayEntries(int $userId): array
    {
        $sql = "SELECT te.*, 
                       te.recorded_at as clock_in_time,
                       (SELECT te2.recorded_at FROM time_entries te2 
                        WHERE te2.user_id = te.user_id 
                        AND te2.type = 'clock_out' 
                        AND DATE(te2.recorded_at) = DATE(te.recorded_at)
                        AND te2.recorded_at > te.recorded_at
                        ORDER BY te2.recorded_at ASC LIMIT 1) as clock_out_time,
                       te.latitude as clock_in_latitude,
                       te.longitude as clock_in_longitude
                FROM time_entries te
                WHERE te.user_id = ? 
                AND DATE(te.recorded_at) = CURDATE()
                AND te.type = 'clock_in'
                ORDER BY te.recorded_at ASC";
        
        $results = self::db()->fetchAll($sql, [$userId]);
        
        // Calcular horas para cada entrada
        foreach ($results as &$entry) {
            if ($entry['clock_out_time']) {
                $diff = strtotime($entry['clock_out_time']) - strtotime($entry['clock_in_time']);
                $entry['total_hours'] = round($diff / 3600, 2);
            } else {
                $entry['total_hours'] = null;
            }
        }
        
        return $results;
    }

    /**
     * Calcula horas trabajadas del día
     */
    public static function calculateDailyHours(int $userId, string $date): float
    {
        $sql = "SELECT * FROM time_entries 
                WHERE user_id = ? AND DATE(recorded_at) = ?
                ORDER BY recorded_at ASC";
        
        $entries = self::db()->fetchAll($sql, [$userId, $date]);
        
        $totalMinutes = 0;
        $clockIn = null;
        $breakStart = null;

        foreach ($entries as $entry) {
            switch ($entry['type']) {
                case 'clock_in':
                    $clockIn = strtotime($entry['recorded_at']);
                    break;
                    
                case 'clock_out':
                    if ($clockIn) {
                        $clockOut = strtotime($entry['recorded_at']);
                        $totalMinutes += ($clockOut - $clockIn) / 60;
                        $clockIn = null;
                    }
                    break;
                    
                case 'break_start':
                    $breakStart = strtotime($entry['recorded_at']);
                    break;
                    
                case 'break_end':
                    if ($breakStart) {
                        $breakEnd = strtotime($entry['recorded_at']);
                        $totalMinutes -= ($breakEnd - $breakStart) / 60;
                        $breakStart = null;
                    }
                    break;
            }
        }

        return round($totalMinutes / 60, 2);
    }

    /**
     * Obtiene resumen semanal
     */
    public static function getWeeklySummary(int $userId): array
    {
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
        
        $summary = [];
        $current = strtotime($startOfWeek);
        $end = strtotime($endOfWeek);
        
        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            $summary[$date] = self::calculateDailyHours($userId, $date);
            $current = strtotime('+1 day', $current);
        }
        
        return $summary;
    }
}
