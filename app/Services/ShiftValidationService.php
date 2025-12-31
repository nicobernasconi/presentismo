<?php
namespace App\Services;

use App\Models\Shift;
use App\Models\ShiftTimeBlock;
use App\Models\User;
use Core\Database;

/**
 * Servicio para validar fichajes contra turnos asignados
 */
class ShiftValidationService
{
    /**
     * Constantes para estados de fichaje
     */
    const STATUS_ON_TIME = 'on_time';           // Dentro del horario
    const STATUS_EARLY = 'early';               // Antes del turno
    const STATUS_LATE = 'late';                 // Después de inicio del turno
    const STATUS_OVERTIME = 'overtime';         // Después de fin del turno
    const STATUS_OUT_OF_SHIFT = 'out_of_shift'; // Sin turno para hoy
    const STATUS_NO_SHIFT = 'no_shift';         // Sin turno asignado
    const STATUS_WRONG_DAY = 'wrong_day';       // Día no laborable
    
    /**
     * Valida un fichaje de entrada
     * 
     * @param int $userId ID del usuario
     * @param string|null $clockTime Hora del fichaje (null = hora actual)
     * @return array Resultado de la validación
     */
    public static function validateClockIn(int $userId, ?string $clockTime = null): array
    {
        $clockTime = $clockTime ?? date('H:i:s');
        $currentDate = date('Y-m-d');
        $dayOfWeek = (int) date('N'); // 1=Lunes, 7=Domingo
        
        // Obtener turnos asignados al usuario
        $shifts = self::getUserActiveShifts($userId);
        
        if (empty($shifts)) {
            return [
                'valid' => true, // Permitir fichar pero registrar warning
                'status' => self::STATUS_NO_SHIFT,
                'message' => 'No tienes turno asignado',
                'shift' => null,
                'expected_time' => null,
                'difference_minutes' => null,
                'warning' => true,
            ];
        }
        
        // Buscar un turno que aplique para hoy
        $applicableShift = null;
        $shiftInfo = null;
        
        foreach ($shifts as $shift) {
            $info = self::getShiftInfoForDay($shift, $dayOfWeek, $clockTime);
            if ($info['applies_today']) {
                $applicableShift = $shift;
                $shiftInfo = $info;
                break;
            }
        }
        
        if (!$applicableShift) {
            return [
                'valid' => true, // Permitir fichar pero registrar warning
                'status' => self::STATUS_WRONG_DAY,
                'message' => 'Hoy no es día laborable según tu turno',
                'shift' => $shifts[0] ?? null, // Mostrar primer turno como referencia
                'expected_time' => null,
                'difference_minutes' => null,
                'warning' => true,
            ];
        }
        
        // Calcular diferencia con hora de entrada esperada
        $clockSeconds = strtotime($clockTime);
        $expectedStart = strtotime($shiftInfo['start_time']);
        $expectedEnd = strtotime($shiftInfo['end_time']);
        
        // Manejar turnos nocturnos
        if ($shiftInfo['is_night_shift'] && $expectedEnd < $expectedStart) {
            $expectedEnd += 86400; // Añadir 24h
        }
        
        $differenceMinutes = round(($clockSeconds - $expectedStart) / 60);
        
        // Obtener tolerancias
        $toleranceEarly = $applicableShift->tolerance_early ?? 15;
        $toleranceLate = $applicableShift->tolerance_late ?? 10;
        
        // Determinar estado
        if ($differenceMinutes < -$toleranceEarly) {
            // Muy temprano (más de X minutos antes)
            $status = self::STATUS_EARLY;
            $message = sprintf('Fichaje anticipado: %d minutos antes del turno', abs($differenceMinutes));
            $warning = true;
        } elseif ($differenceMinutes <= $toleranceLate) {
            // A tiempo (dentro de tolerancia)
            $status = self::STATUS_ON_TIME;
            $message = 'Fichaje dentro del horario';
            $warning = false;
        } elseif ($differenceMinutes > $toleranceLate && $clockSeconds < $expectedEnd) {
            // Tarde pero dentro del turno
            $status = self::STATUS_LATE;
            $message = sprintf('Fichaje tardío: %d minutos después del inicio', $differenceMinutes);
            $warning = true;
        } else {
            // Fuera del turno (después del fin)
            $status = self::STATUS_OVERTIME;
            $message = 'Fichaje fuera del horario del turno';
            $warning = true;
        }
        
        return [
            'valid' => true,
            'status' => $status,
            'message' => $message,
            'shift' => $applicableShift,
            'shift_name' => $applicableShift->name,
            'expected_time' => $shiftInfo['start_time'],
            'actual_time' => $clockTime,
            'difference_minutes' => $differenceMinutes,
            'warning' => $warning,
            'tolerance_early' => $toleranceEarly,
            'tolerance_late' => $toleranceLate,
        ];
    }
    
    /**
     * Valida un fichaje de salida
     * 
     * @param int $userId ID del usuario
     * @param string|null $clockTime Hora del fichaje (null = hora actual)
     * @return array Resultado de la validación
     */
    public static function validateClockOut(int $userId, ?string $clockTime = null): array
    {
        $clockTime = $clockTime ?? date('H:i:s');
        $dayOfWeek = (int) date('N');
        
        // Obtener turnos asignados al usuario
        $shifts = self::getUserActiveShifts($userId);
        
        if (empty($shifts)) {
            return [
                'valid' => true,
                'status' => self::STATUS_NO_SHIFT,
                'message' => 'No tienes turno asignado',
                'shift' => null,
                'expected_time' => null,
                'difference_minutes' => null,
                'warning' => true,
            ];
        }
        
        // Buscar turno aplicable
        $applicableShift = null;
        $shiftInfo = null;
        
        foreach ($shifts as $shift) {
            $info = self::getShiftInfoForDay($shift, $dayOfWeek, $clockTime);
            if ($info['applies_today']) {
                $applicableShift = $shift;
                $shiftInfo = $info;
                break;
            }
        }
        
        if (!$applicableShift) {
            return [
                'valid' => true,
                'status' => self::STATUS_WRONG_DAY,
                'message' => 'Hoy no es día laborable según tu turno',
                'shift' => $shifts[0] ?? null,
                'expected_time' => null,
                'difference_minutes' => null,
                'warning' => true,
            ];
        }
        
        // Calcular diferencia con hora de salida esperada
        $clockSeconds = strtotime($clockTime);
        $expectedEnd = strtotime($shiftInfo['end_time']);
        $expectedStart = strtotime($shiftInfo['start_time']);
        
        // Manejar turnos nocturnos
        if ($shiftInfo['is_night_shift'] && $expectedEnd < $expectedStart) {
            $expectedEnd += 86400;
        }
        
        $differenceMinutes = round(($clockSeconds - $expectedEnd) / 60);
        
        // Obtener tolerancia
        $toleranceEarly = $applicableShift->tolerance_early ?? 15;
        
        // Determinar estado
        if ($differenceMinutes < -30) {
            // Salida muy temprana (más de 30 min antes)
            $status = self::STATUS_EARLY;
            $message = sprintf('Salida anticipada: %d minutos antes del fin del turno', abs($differenceMinutes));
            $warning = true;
        } elseif ($differenceMinutes >= -30 && $differenceMinutes <= 30) {
            // Salida a tiempo (dentro de tolerancia de 30 min)
            $status = self::STATUS_ON_TIME;
            $message = 'Salida dentro del horario';
            $warning = false;
        } else {
            // Horas extra
            $status = self::STATUS_OVERTIME;
            $message = sprintf('Salida con %d minutos extra', $differenceMinutes);
            $warning = false; // Las horas extra no son necesariamente un problema
        }
        
        return [
            'valid' => true,
            'status' => $status,
            'message' => $message,
            'shift' => $applicableShift,
            'shift_name' => $applicableShift->name,
            'expected_time' => $shiftInfo['end_time'],
            'actual_time' => $clockTime,
            'difference_minutes' => $differenceMinutes,
            'warning' => $warning,
        ];
    }
    
    /**
     * Obtiene los turnos activos de un usuario
     */
    private static function getUserActiveShifts(int $userId): array
    {
        $db = Database::getInstance();
        
        $sql = "SELECT s.* FROM shifts s
                JOIN shift_assignments sa ON s.id = sa.shift_id
                WHERE sa.user_id = ? 
                AND sa.is_active = 1
                AND sa.start_date <= CURDATE()
                AND (sa.end_date IS NULL OR sa.end_date >= CURDATE())
                AND s.deleted_at IS NULL
                AND s.is_active = 1";
        
        $results = $db->fetchAll($sql, [$userId]);
        
        return array_map(fn($data) => Shift::hydrate($data), $results);
    }
    
    /**
     * Obtiene información del turno para un día específico
     */
    private static function getShiftInfoForDay(Shift $shift, int $dayOfWeek, string $currentTime): array
    {
        // Verificar si el turno usa bloques de tiempo
        if ($shift->usesTimeBlocks()) {
            return self::getTimeBlockInfoForDay($shift, $dayOfWeek, $currentTime);
        }
        
        // Turno tradicional
        $workingDays = $shift->getWorkingDays();
        $appliesToday = in_array($dayOfWeek, $workingDays);
        
        return [
            'applies_today' => $appliesToday,
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'is_night_shift' => (bool) $shift->is_night_shift,
            'break_duration' => $shift->break_duration ?? 0,
        ];
    }
    
    /**
     * Obtiene información de bloques de tiempo para un día
     */
    private static function getTimeBlockInfoForDay(Shift $shift, int $dayOfWeek, string $currentTime): array
    {
        $blocks = ShiftTimeBlock::getByShift($shift->id);
        
        // Filtrar bloques de trabajo para el día actual
        $todayBlocks = array_filter($blocks, function($block) use ($dayOfWeek) {
            return $block->day_of_week == $dayOfWeek && $block->isWorkBlock();
        });
        
        if (empty($todayBlocks)) {
            return [
                'applies_today' => false,
                'start_time' => null,
                'end_time' => null,
                'is_night_shift' => false,
                'break_duration' => 0,
            ];
        }
        
        // Obtener primer y último bloque de trabajo
        $todayBlocks = array_values($todayBlocks);
        usort($todayBlocks, fn($a, $b) => strtotime($a->start_time) - strtotime($b->start_time));
        
        $firstBlock = $todayBlocks[0];
        $lastBlock = end($todayBlocks);
        
        return [
            'applies_today' => true,
            'start_time' => $firstBlock->start_time,
            'end_time' => $lastBlock->end_time,
            'is_night_shift' => (bool) ($lastBlock->spans_next_day ?? false),
            'break_duration' => 0, // Los bloques ya excluyen descansos
            'blocks' => $todayBlocks,
        ];
    }
    
    /**
     * Genera un resumen del estado de fichaje para mostrar al usuario
     */
    public static function getClockStatusSummary(int $userId): array
    {
        $shifts = self::getUserActiveShifts($userId);
        $dayOfWeek = (int) date('N');
        $currentTime = date('H:i:s');
        
        if (empty($shifts)) {
            return [
                'has_shift' => false,
                'message' => 'Sin turno asignado',
                'current_shift' => null,
                'is_working_day' => false,
            ];
        }
        
        foreach ($shifts as $shift) {
            $info = self::getShiftInfoForDay($shift, $dayOfWeek, $currentTime);
            if ($info['applies_today']) {
                return [
                    'has_shift' => true,
                    'message' => sprintf('Turno: %s (%s - %s)', 
                        $shift->name,
                        date('H:i', strtotime($info['start_time'])),
                        date('H:i', strtotime($info['end_time']))
                    ),
                    'current_shift' => $shift,
                    'shift_info' => $info,
                    'is_working_day' => true,
                ];
            }
        }
        
        return [
            'has_shift' => true,
            'message' => 'Hoy no es día laborable',
            'current_shift' => $shifts[0],
            'is_working_day' => false,
        ];
    }
}
