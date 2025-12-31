<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Shift (Turno)
 */
class Shift extends Model
{
    protected static string $table = 'shifts';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'name',
        'code',
        'color',
        'start_time',
        'end_time',
        'break_duration',
        'break_start_time',
        'is_flexible',
        'flexible_margin',
        'tolerance_early',
        'tolerance_late',
        'working_days',
        'is_night_shift',
        'is_active',
        'use_time_blocks',
    ];

    /**
     * Obtiene los días de trabajo como array
     */
    public function getWorkingDays(): array
    {
        return json_decode($this->working_days ?? '[1,2,3,4,5]', true);
    }

    /**
     * Nombres de días de trabajo
     */
    public function getWorkingDaysNames(): string
    {
        $days = $this->getWorkingDays();
        $dayNames = [
            1 => 'L', 2 => 'M', 3 => 'X', 
            4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'
        ];
        
        return implode('-', array_map(fn($d) => $dayNames[$d] ?? '', $days));
    }

    /**
     * Calcula horas diarias del turno
     */
    public function getDailyHours(): float
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        
        // Si es turno nocturno, añadir 24h al final
        if ($this->is_night_shift || $end <= $start) {
            $end += 86400;
        }
        
        $minutes = ($end - $start) / 60;
        $minutes -= $this->break_duration;
        
        return round($minutes / 60, 2);
    }

    /**
     * Horas semanales
     */
    public function getWeeklyHours(): float
    {
        return $this->getDailyHours() * count($this->getWorkingDays());
    }

    /**
     * Formato de horario
     */
    public function getScheduleFormatted(): string
    {
        $start = date('H:i', strtotime($this->start_time));
        $end = date('H:i', strtotime($this->end_time));
        return "{$start} - {$end}";
    }

    /**
     * Verifica si hoy es día de trabajo
     */
    public function isWorkingToday(): bool
    {
        $dayOfWeek = (int) date('N'); // 1=Lunes, 7=Domingo
        return in_array($dayOfWeek, $this->getWorkingDays());
    }

    /**
     * Obtiene usuarios asignados al turno
     */
    public function assignedUsers(): array
    {
        $sql = "SELECT u.* FROM users u
                JOIN shift_assignments sa ON u.id = sa.user_id
                WHERE sa.shift_id = ? 
                AND sa.is_active = 1
                AND sa.start_date <= CURDATE()
                AND (sa.end_date IS NULL OR sa.end_date >= CURDATE())
                AND u.deleted_at IS NULL";
        
        $results = self::db()->fetchAll($sql, [$this->id]);
        
        return array_map(fn($data) => User::hydrate($data), $results);
    }

    /**
     * Verifica si usa bloques de tiempo
     */
    public function usesTimeBlocks(): bool
    {
        return (bool) ($this->use_time_blocks ?? false);
    }

    /**
     * Obtiene los bloques de tiempo del turno
     */
    public function timeBlocks(): array
    {
        if (!$this->usesTimeBlocks()) {
            return [];
        }
        return ShiftTimeBlock::getByShift($this->id);
    }

    /**
     * Obtiene bloques agrupados por día
     */
    public function timeBlocksByDay(): array
    {
        if (!$this->usesTimeBlocks()) {
            return [];
        }
        return ShiftTimeBlock::groupedByDay($this->id);
    }

    /**
     * Obtiene el horario formateado (tradicional o con bloques)
     */
    public function getScheduleDisplay(): string
    {
        if ($this->usesTimeBlocks()) {
            $blocks = $this->timeBlocksByDay();
            if (empty($blocks)) {
                return 'Sin horario definido';
            }
            
            $days = [];
            foreach ($blocks as $dayNum => $dayBlocks) {
                $dayName = ShiftTimeBlock::dayNamesShort()[$dayNum] ?? '';
                $workBlocks = array_filter($dayBlocks, fn($b) => $b->isWorkBlock());
                if (!empty($workBlocks)) {
                    $days[] = $dayName;
                }
            }
            return implode(', ', $days);
        }
        
        return $this->getScheduleFormatted();
    }

    /**
     * Calcula horas si usa bloques de tiempo
     */
    public function calculateHoursFromBlocks(): float
    {
        if (!$this->usesTimeBlocks()) {
            return $this->getWeeklyHours();
        }
        return ShiftTimeBlock::calculateWeeklyHours($this->id);
    }

    /**
     * Turnos para select
     */
    public static function forSelect(): array
    {
        $shifts = self::where('is_active', 1)->get();
        $options = ['' => 'Sin turno asignado'];
        
        foreach ($shifts as $shift) {
            if ($shift->usesTimeBlocks()) {
                $options[$shift->id] = $shift->name . ' (Horario flexible)';
            } else {
                $options[$shift->id] = $shift->name . ' (' . $shift->getScheduleFormatted() . ')';
            }
        }
        
        return $options;
    }
}
