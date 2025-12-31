<?php
namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Modelo User (Usuario/Empleado)
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'department_id',
        'work_center_id',
        'role_id',
        'email',
        'password',
        'employee_code',
        'name',
        'first_name',
        'last_name',
        'dni',
        'phone',
        'mobile',
        'birth_date',
        'gender',
        'address',
        'city',
        'postal_code',
        'avatar_path',
        'position',
        'hire_date',
        'termination_date',
        'contract_type',
        'hours_per_week',
        'settings',
        'language',
        'is_active',
    ];

    /**
     * Encuentra usuario por email (sin tenant scope)
     */
    public static function findByEmail(string $email): ?self
    {
        $sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1";
        $data = self::db()->fetch($sql, [$email]);
        
        return $data ? static::hydrate($data) : null;
    }

    /**
     * Obtiene el tenant
     */
    public function tenant(): ?Tenant
    {
        return Tenant::find($this->tenant_id);
    }

    /**
     * Obtiene el nombre de la empresa del usuario
     */
    public function tenantName(): string
    {
        $tenant = $this->tenant();
        return $tenant ? $tenant->name : 'N/A';
    }

    /**
     * Verifica si es superadmin de una empresa
     */
    public function isTenantAdmin(): bool
    {
        return $this->role_id == 1;
    }

    /**
     * Obtiene el departamento
     */
    public function department(): ?Department
    {
        if (!$this->department_id) return null;
        return Department::find($this->department_id);
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
     * Obtiene el rol
     */
    public function role(): ?Role
    {
        return Role::find($this->role_id);
    }

    /**
     * Verifica si tiene un rol específico
     */
    public function hasRole(string $slug): bool
    {
        $role = $this->role();
        return $role && $role->slug === $slug;
    }

    /**
     * Verifica si es admin o superior
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->hasRole('admin');
    }

    /**
     * Verifica si es supervisor o superior
     */
    public function isSupervisor(): bool
    {
        return $this->isAdmin() || $this->hasRole('supervisor');
    }

    /**
     * Obtiene los fichajes del día
     */
    public function todayTimeEntries(): array
    {
        return TimeEntry::where('user_id', $this->id)
            ->where('DATE(recorded_at)', date('Y-m-d'), '=')
            ->orderBy('recorded_at', 'ASC')
            ->get();
    }

    /**
     * Obtiene el estado actual de fichaje (string simple)
     */
    public function getCurrentClockStatus(): string
    {
        $sql = "SELECT type FROM time_entries 
                WHERE user_id = ? AND DATE(recorded_at) = CURDATE() 
                ORDER BY recorded_at DESC LIMIT 1";
        
        $result = self::db()->fetch($sql, [$this->id]);
        
        if (!$result) return 'out';
        
        // Compatibilidad con PHP 7.4
        switch ($result['type']) {
            case 'clock_in':
                return 'in';
            case 'clock_out':
                return 'out';
            case 'break_start':
                return 'break';
            case 'break_end':
                return 'in';
            default:
                return 'out';
        }
    }

    /**
     * Obtiene información detallada del estado de fichaje
     */
    public function getClockStatusDetails(): array
    {
        $sql = "SELECT * FROM time_entries 
                WHERE user_id = ? AND DATE(recorded_at) = CURDATE() 
                ORDER BY recorded_at ASC";
        
        $entries = self::db()->fetchAll($sql, [$this->id]);
        
        $status = [
            'is_clocked_in' => false,
            'clock_in_time' => null,
            'clock_out_time' => null,
            'elapsed_time' => '0:00',
            'status' => 'out'
        ];
        
        if (empty($entries)) {
            return $status;
        }
        
        $clockInTime = null;
        $lastType = null;
        
        foreach ($entries as $entry) {
            $lastType = $entry['type'];
            if ($entry['type'] === 'clock_in' && !$clockInTime) {
                $clockInTime = $entry['recorded_at'];
            } elseif ($entry['type'] === 'clock_out') {
                $status['clock_out_time'] = $entry['recorded_at'];
            }
        }
        
        $status['clock_in_time'] = $clockInTime;
        $status['is_clocked_in'] = ($lastType === 'clock_in' || $lastType === 'break_end');
        $status['status'] = $status['is_clocked_in'] ? 'in' : 'out';
        
        // Calcular tiempo transcurrido si está fichado
        if ($status['is_clocked_in'] && $clockInTime) {
            $start = new \DateTime($clockInTime);
            $now = new \DateTime();
            $diff = $now->diff($start);
            $status['elapsed_time'] = $diff->format('%H:%I');
        }
        
        return $status;
    }

    /**
     * Obtiene el turno asignado activo
     */
    public function currentShift(): ?Shift
    {
        $sql = "SELECT s.* FROM shifts s
                JOIN shift_assignments sa ON s.id = sa.shift_id
                WHERE sa.user_id = ? 
                AND sa.is_active = 1
                AND sa.start_date <= CURDATE()
                AND (sa.end_date IS NULL OR sa.end_date >= CURDATE())
                AND s.deleted_at IS NULL
                LIMIT 1";
        
        $data = self::db()->fetch($sql, [$this->id]);
        
        return $data ? Shift::hydrate($data) : null;
    }

    /**
     * Obtiene todas las asignaciones de turno del empleado
     */
    public function shiftAssignments(): array
    {
        $sql = "SELECT sa.*, s.name as shift_name, s.start_time, s.end_time
                FROM shift_assignments sa
                JOIN shifts s ON sa.shift_id = s.id
                WHERE sa.user_id = ?
                ORDER BY sa.start_date DESC";
        
        return self::db()->fetchAll($sql, [$this->id]);
    }

    /**
     * Asigna un turno al empleado
     */
    public function assignShift(int $shiftId, string $startDate, ?string $endDate = null, ?string $notes = null): bool
    {
        $sql = "INSERT INTO shift_assignments (tenant_id, user_id, shift_id, start_date, end_date, notes, created_by, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $createdBy = $_SESSION['user_id'] ?? null;
        
        self::db()->query($sql, [
            $this->tenant_id,
            $this->id,
            $shiftId,
            $startDate,
            $endDate,
            $notes,
            $createdBy
        ]);
        return true;
    }

    /**
     * Desactiva la asignación de turno actual
     */
    public function deactivateCurrentShift(): bool
    {
        $sql = "UPDATE shift_assignments 
                SET is_active = 0, updated_at = NOW()
                WHERE user_id = ? 
                AND is_active = 1
                AND start_date <= CURDATE()
                AND (end_date IS NULL OR end_date >= CURDATE())";
        
        return self::db()->query($sql, [$this->id])->rowCount() >= 0;
    }

    /**
     * Verifica si el empleado tiene un turno asignado
     */
    public function hasActiveShift(): bool
    {
        return $this->currentShift() !== null;
    }

    /**
     * Verifica si el empleado puede fichar a la hora actual
     */
    public function canClockInNow(): array
    {
        $result = [
            'can_clock_in' => false,
            'reason' => '',
            'shift' => null
        ];

        $shift = $this->currentShift();
        
        if (!$shift) {
            $result['reason'] = 'No tiene turno asignado';
            return $result;
        }

        $result['shift'] = $shift;
        $currentTime = date('H:i:s');
        $currentDay = (int)date('N'); // 1=Lunes, 7=Domingo

        // Verificar si hoy es día laboral
        $workingDays = json_decode($shift->working_days ?? '[1,2,3,4,5]', true);
        if (!in_array($currentDay, $workingDays)) {
            $result['reason'] = 'Hoy no es un día laboral según su turno';
            return $result;
        }

        // Calcular ventana de fichaje con tolerancia
        $shiftStart = new \DateTime($shift->start_time);
        $toleranceEarly = $shift->tolerance_early ?? 5;
        $toleranceLate = $shift->tolerance_late ?? 5;
        
        $earliestTime = clone $shiftStart;
        $earliestTime->modify("-{$toleranceEarly} minutes");
        
        $latestTime = clone $shiftStart;
        $latestTime->modify("+{$toleranceLate} minutes");

        $current = new \DateTime($currentTime);

        if ($current < $earliestTime) {
            $result['reason'] = 'Es demasiado temprano para fichar. Puede fichar desde las ' . $earliestTime->format('H:i');
            return $result;
        }

        if ($current > $latestTime) {
            $result['reason'] = 'Llegada con retraso. Se registrará el fichaje pero puede requerir justificación.';
            $result['can_clock_in'] = true;
            $result['warning'] = true;
            return $result;
        }

        $result['can_clock_in'] = true;
        $result['reason'] = 'Puede fichar normalmente';
        return $result;
    }

    /**
     * Verifica si el empleado puede fichar salida a la hora actual
     */
    public function canClockOutNow(): array
    {
        $result = [
            'can_clock_out' => false,
            'reason' => '',
            'shift' => null
        ];

        $shift = $this->currentShift();
        
        if (!$shift) {
            $result['can_clock_out'] = true;
            $result['reason'] = 'No tiene turno asignado, puede fichar libremente';
            return $result;
        }

        $result['shift'] = $shift;
        $currentTime = date('H:i:s');

        // Calcular hora mínima de salida
        $shiftEnd = new \DateTime($shift->end_time);
        $toleranceEarly = $shift->tolerance_early ?? 5;
        
        $earliestExit = clone $shiftEnd;
        $earliestExit->modify("-{$toleranceEarly} minutes");

        $current = new \DateTime($currentTime);

        if ($current < $earliestExit) {
            $result['reason'] = 'Es demasiado temprano para fichar salida. Puede salir desde las ' . $earliestExit->format('H:i');
            $result['can_clock_out'] = true;
            $result['warning'] = true;
            return $result;
        }

        $result['can_clock_out'] = true;
        $result['reason'] = 'Puede fichar salida normalmente';
        return $result;
    }

    /**
     * Obtiene el turno para una fecha específica
     */
    public function getShiftForDate(string $date): ?Shift
    {
        $sql = "SELECT s.* FROM shifts s
                JOIN shift_assignments sa ON s.id = sa.shift_id
                WHERE sa.user_id = ? 
                AND sa.is_active = 1
                AND sa.start_date <= ?
                AND (sa.end_date IS NULL OR sa.end_date >= ?)
                AND s.deleted_at IS NULL
                LIMIT 1";
        
        $data = self::db()->fetch($sql, [$this->id, $date, $date]);
        
        return $data ? Shift::hydrate($data) : null;
    }

    /**
     * Obtiene el balance de vacaciones del año actual
     */
    public function holidayBalance(): ?Holiday
    {
        return Holiday::where('user_id', $this->id)
            ->where('year', date('Y'))
            ->first();
    }

    /**
     * Nombre completo
     */
    public function getFullName(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name;
    }

    /**
     * Iniciales para avatar
     */
    public function getInitials(): string
    {
        $parts = explode(' ', $this->name);
        $initials = '';
        
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        
        return $initials;
    }
}
