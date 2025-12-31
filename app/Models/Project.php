<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Project (Proyecto)
 */
class Project extends Model
{
    protected static string $table = 'projects';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'color',
        'client_name',
        'budget_hours',
        'start_date',
        'end_date',
        'status',
        'is_billable',
        'hourly_rate',
        'manager_id',
        'is_active',
    ];

    /**
     * Obtiene el manager
     */
    public function manager(): ?User
    {
        if (!$this->manager_id) return null;
        return User::find($this->manager_id);
    }

    /**
     * Alias para manager()
     */
    public function projectManager(): ?User
    {
        return $this->manager();
    }

    /**
     * Obtiene las tareas del proyecto
     */
    public function tasks(): array
    {
        return Task::where('project_id', $this->id)->get();
    }

    /**
     * Cuenta tareas
     */
    public function taskCount(): int
    {
        return Task::count('project_id = ?', [$this->id]);
    }

    /**
     * Etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case 'pending': return 'Pendiente';
            case 'active': return 'Activo';
            case 'paused': return 'Pausado';
            case 'completed': return 'Completado';
            case 'cancelled': return 'Cancelado';
            default: return $this->status;
        }
    }

    /**
     * Color del estado
     */
    public function getStatusColor(): string
    {
        switch ($this->status) {
            case 'pending': return 'gray';
            case 'active': return 'green';
            case 'paused': return 'yellow';
            case 'completed': return 'blue';
            case 'cancelled': return 'red';
            default: return 'gray';
        }
    }

    /**
     * Calcula horas registradas
     */
    public function getLoggedHours(): float
    {
        $sql = "SELECT SUM(
                    TIMESTAMPDIFF(MINUTE, 
                        (SELECT recorded_at FROM time_entries te2 
                         WHERE te2.project_id = ? 
                         AND te2.type = 'clock_in' 
                         AND te2.user_id = te.user_id 
                         AND DATE(te2.recorded_at) = DATE(te.recorded_at)
                         ORDER BY te2.recorded_at DESC LIMIT 1),
                        te.recorded_at
                    )
                ) as minutes
                FROM time_entries te
                WHERE te.project_id = ? AND te.type = 'clock_out'";
        
        // Simplificado: contar entradas con proyecto
        $sql = "SELECT COUNT(*) as count FROM time_entries WHERE project_id = ?";
        $result = self::db()->fetch($sql, [$this->id]);
        
        return 0; // TODO: Implementar cálculo real
    }

    /**
     * Proyectos para select
     */
    public static function forSelect(): array
    {
        $projects = self::where('is_active', 1)
            ->where('status', 'active')
            ->get();
        
        $options = ['' => 'Sin proyecto'];
        
        foreach ($projects as $project) {
            $options[$project->id] = $project->name;
        }
        
        return $options;
    }
}
