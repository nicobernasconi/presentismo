<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Holiday (Balance de Vacaciones)
 */
class Holiday extends Model
{
    protected static string $table = 'holidays';
    
    protected static array $fillable = [
        'tenant_id',
        'user_id',
        'year',
        'entitled_days',
        'used_days',
        'pending_days',
        'carried_over',
        'extra_days',
        'notes',
    ];

    /**
     * Obtiene el usuario
     */
    public function user(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Total de días disponibles
     */
    public function getTotalAvailable(): float
    {
        return $this->entitled_days + $this->carried_over + $this->extra_days;
    }

    /**
     * Días restantes
     */
    public function getRemaining(): float
    {
        return $this->getTotalAvailable() - $this->used_days - $this->pending_days;
    }

    /**
     * Porcentaje utilizado
     */
    public function getUsagePercentage(): float
    {
        $total = $this->getTotalAvailable();
        if ($total <= 0) return 0;
        
        return round(($this->used_days / $total) * 100, 1);
    }

    /**
     * Obtiene o crea balance para un usuario y año
     */
    public static function getOrCreate(int $userId, int $year = null): self
    {
        $year = $year ?? (int) date('Y');
        $tenantId = $_SESSION['tenant_id'] ?? null;
        
        $sql = "SELECT * FROM holidays 
                WHERE user_id = ? AND year = ? AND tenant_id = ?
                LIMIT 1";
        
        $data = self::db()->fetch($sql, [$userId, $year, $tenantId]);
        
        if ($data) {
            return static::hydrate($data);
        }
        
        // Crear nuevo balance con días por defecto
        return self::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'year' => $year,
            'entitled_days' => 22, // Días mínimos legales en España
            'used_days' => 0,
            'pending_days' => 0,
            'carried_over' => 0,
            'extra_days' => 0,
        ]);
    }

    /**
     * Incrementa días usados
     */
    public function addUsedDays(float $days): bool
    {
        $this->used_days += $days;
        return $this->save();
    }

    /**
     * Incrementa días pendientes
     */
    public function addPendingDays(float $days): bool
    {
        $this->pending_days += $days;
        return $this->save();
    }

    /**
     * Convierte pendientes a usados (al aprobar)
     */
    public function confirmPending(float $days): bool
    {
        $this->pending_days -= $days;
        $this->used_days += $days;
        return $this->save();
    }

    /**
     * Devuelve días pendientes (al rechazar/cancelar)
     */
    public function releasePending(float $days): bool
    {
        $this->pending_days -= $days;
        return $this->save();
    }
}
