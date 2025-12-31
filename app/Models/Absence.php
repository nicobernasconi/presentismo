<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Absence (Ausencia)
 */
class Absence extends Model
{
    protected static string $table = 'absences';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'user_id',
        'absence_type_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'total_days',
        'total_hours',
        'reason',
        'document_path',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Obtiene el usuario
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Obtiene el tipo de ausencia
     */
    public function absenceType(): ?AbsenceType
    {
        return AbsenceType::find($this->absence_type_id);
    }

    /**
     * Obtiene quien aprobó
     */
    public function approvedBy(): ?User
    {
        if (!$this->approved_by) return null;
        return User::find($this->approved_by);
    }

    /**
     * Etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case 'pending': return 'Pendiente';
            case 'approved': return 'Aprobada';
            case 'rejected': return 'Rechazada';
            case 'cancelled': return 'Cancelada';
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
            case 'cancelled': return 'gray';
            default: return 'gray';
        }
    }

    /**
     * Rango de fechas formateado
     */
    public function getDateRange(): string
    {
        $start = date('d/m/Y', strtotime($this->start_date));
        $end = date('d/m/Y', strtotime($this->end_date));
        
        if ($start === $end) {
            return $start;
        }
        
        return "{$start} - {$end}";
    }

    /**
     * Aprueba la ausencia
     */
    public function approve(int $approvedBy): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $approvedBy;
        $this->approved_at = date('Y-m-d H:i:s');
        
        return $this->save();
    }

    /**
     * Rechaza la ausencia
     */
    public function reject(int $rejectedBy, string $reason = null): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->approved_by = $rejectedBy;
        $this->approved_at = date('Y-m-d H:i:s');
        $this->rejection_reason = $reason;
        
        return $this->save();
    }

    /**
     * Cancela la ausencia
     */
    public function cancel(): bool
    {
        $this->status = self::STATUS_CANCELLED;
        return $this->save();
    }

    /**
     * Obtiene ausencias pendientes de aprobación
     */
    public static function pending(): array
    {
        return self::where('status', self::STATUS_PENDING)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Verifica solapamiento con otras ausencias
     */
    public static function hasOverlap(int $userId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM absences 
                WHERE user_id = ? 
                AND status IN ('pending', 'approved')
                AND start_date <= ? 
                AND end_date >= ?
                AND deleted_at IS NULL";
        
        $params = [$userId, $endDate, $startDate];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = self::db()->fetch($sql, $params);
        
        return $result['count'] > 0;
    }
}
