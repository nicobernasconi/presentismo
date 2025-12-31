<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo ShiftTimeBlock
 * Representa un bloque de tiempo específico para un día de la semana dentro de un turno
 */
class ShiftTimeBlock extends Model
{
    protected static string $table = 'shift_time_blocks';
    
    protected static array $fillable = [
        'shift_id',
        'day_of_week',
        'start_time',
        'end_time',
        'spans_next_day',
        'block_type',
        'order_index',
    ];

    /**
     * Nombres de días de la semana
     */
    public static function dayNames(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    /**
     * Nombres cortos de días
     */
    public static function dayNamesShort(): array
    {
        return [
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mié',
            4 => 'Jue',
            5 => 'Vie',
            6 => 'Sáb',
            7 => 'Dom',
        ];
    }

    /**
     * Obtiene el nombre del día
     */
    public function getDayName(): string
    {
        return self::dayNames()[$this->day_of_week] ?? '';
    }

    /**
     * Obtiene el nombre corto del día
     */
    public function getDayNameShort(): string
    {
        return self::dayNamesShort()[$this->day_of_week] ?? '';
    }

    /**
     * Verifica si es un bloque de trabajo
     */
    public function isWorkBlock(): bool
    {
        return $this->block_type === 'work';
    }

    /**
     * Verifica si es un bloque de descanso
     */
    public function isBreakBlock(): bool
    {
        return $this->block_type === 'break';
    }

    /**
     * Calcula la duración del bloque en minutos
     * Soporta turnos nocturnos que cruzan medianoche
     */
    public function getDurationMinutes(): int
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        
        // Si spans_next_day=1 o si end < start, el turno cruza medianoche
        if (($this->spans_next_day ?? 0) || $end < $start) {
            // Agregar 24 horas al tiempo final
            $end += 86400; // 24 * 60 * 60
        }
        
        return ($end - $start) / 60;
    }

    /**
     * Formatea la duración como string
     */
    public function getFormattedDuration(): string
    {
        $minutes = $this->getDurationMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$mins}m";
        }
    }

    /**
     * Formatea el horario del bloque
     */
    public function getFormattedSchedule(): string
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . 
               date('H:i', strtotime($this->end_time));
    }

    /**
     * Obtiene el turno asociado
     */
    public function shift(): ?Shift
    {
        return Shift::find($this->shift_id);
    }

    /**
     * Obtiene todos los bloques de un turno ordenados
     */
    public static function getByShift(int $shiftId): array
    {
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE shift_id = ? 
                ORDER BY day_of_week, order_index, start_time";
        
        $results = self::db()->fetchAll($sql, [$shiftId]);
        return array_map(fn($data) => self::hydrate($data), $results);
    }

    /**
     * Obtiene bloques por día de la semana
     */
    public static function getByDay(int $shiftId, int $dayOfWeek): array
    {
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE shift_id = ? AND day_of_week = ? 
                ORDER BY order_index, start_time";
        
        $results = self::db()->fetchAll($sql, [$shiftId, $dayOfWeek]);
        return array_map(fn($data) => self::hydrate($data), $results);
    }

    /**
     * Agrupa bloques por día
     */
    public static function groupedByDay(int $shiftId): array
    {
        $blocks = self::getByShift($shiftId);
        $grouped = [];
        
        foreach ($blocks as $block) {
            $day = $block->day_of_week;
            if (!isset($grouped[$day])) {
                $grouped[$day] = [];
            }
            $grouped[$day][] = $block;
        }
        
        return $grouped;
    }

    /**
     * Elimina todos los bloques de un turno
     */
    public static function deleteByShift(int $shiftId): void
    {
        self::db()->query(
            "DELETE FROM " . self::$table . " WHERE shift_id = ?",
            [$shiftId]
        );
    }

    /**
     * Calcula las horas totales de trabajo por semana
     */
    public static function calculateWeeklyHours(int $shiftId): float
    {
        $sql = "SELECT SUM(
                    TIMESTAMPDIFF(MINUTE, start_time, end_time)
                ) as total_minutes 
                FROM " . self::$table . " 
                WHERE shift_id = ? AND block_type = 'work'";
        
        $result = self::db()->fetch($sql, [$shiftId]);
        return ($result['total_minutes'] ?? 0) / 60;
    }
}
