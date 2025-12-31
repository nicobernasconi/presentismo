<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Task (Tarea)
 */
class Task extends Model
{
    protected static string $table = 'tasks';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'project_id',
        'parent_id',
        'name',
        'description',
        'estimated_hours',
        'status',
        'priority',
        'due_date',
        'assigned_to',
        'is_active',
    ];

    /**
     * Obtiene el proyecto
     */
    public function project(): ?Project
    {
        return Project::find($this->project_id);
    }

    /**
     * Obtiene la tarea padre
     */
    public function parent(): ?self
    {
        if (!$this->parent_id) return null;
        return self::find($this->parent_id);
    }

    /**
     * Obtiene subtareas
     */
    public function subtasks(): array
    {
        return self::where('parent_id', $this->id)->get();
    }

    /**
     * Obtiene el usuario asignado
     */
    public function assignedUser(): ?User
    {
        if (!$this->assigned_to) return null;
        return User::find($this->assigned_to);
    }

    /**
     * Obtiene el usuario que asignó la tarea (si existe assigned_by)
     */
    public function assignedByUser(): ?User
    {
        if (!isset($this->attributes['assigned_by']) || !$this->attributes['assigned_by']) {
            return null;
        }
        return User::find($this->attributes['assigned_by']);
    }

    /**
     * Etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        switch ($this->status) {
            case 'pending': return 'Pendiente';
            case 'in_progress': return 'En Progreso';
            case 'completed': return 'Completada';
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
            case 'pending': return 'gray';
            case 'in_progress': return 'blue';
            case 'completed': return 'green';
            case 'cancelled': return 'red';
            default: return 'gray';
        }
    }

    /**
     * Etiqueta de prioridad
     */
    public function getPriorityLabel(): string
    {
        switch ($this->priority) {
            case 'low': return 'Baja';
            case 'normal': return 'Normal';
            case 'high': return 'Alta';
            case 'urgent': return 'Urgente';
            default: return $this->priority;
        }
    }

    /**
     * Color de prioridad
     */
    public function getPriorityColor(): string
    {
        switch ($this->priority) {
            case 'low': return 'gray';
            case 'normal': return 'blue';
            case 'high': return 'yellow';
            case 'urgent': return 'red';
            default: return 'gray';
        }
    }

    /**
     * Tareas de un proyecto para select
     */
    public static function forSelectByProject(int $projectId): array
    {
        $tasks = self::where('project_id', $projectId)
            ->where('is_active', 1)
            ->get();
        
        $options = ['' => 'Sin tarea'];
        
        foreach ($tasks as $task) {
            $options[$task->id] = $task->name;
        }
        
        return $options;
    }
}
