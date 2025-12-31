<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Department (Departamento)
 */
class Department extends Model
{
    protected static string $table = 'departments';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'code',
        'description',
        'manager_id',
        'is_active',
    ];

    /**
     * Obtiene el departamento padre
     */
    public function parent(): ?self
    {
        if (!$this->parent_id) return null;
        return self::find($this->parent_id);
    }

    /**
     * Obtiene subdepartamentos
     */
    public function children(): array
    {
        return self::where('parent_id', $this->id)->get();
    }

    /**
     * Obtiene el manager
     */
    public function manager(): ?User
    {
        if (!$this->manager_id) return null;
        return User::find($this->manager_id);
    }

    /**
     * Obtiene empleados del departamento
     */
    public function employees(): array
    {
        return User::where('department_id', $this->id)
            ->where('is_active', 1)
            ->get();
    }

    /**
     * Cuenta empleados
     */
    public function employeeCount(): int
    {
        return User::count('department_id = ? AND is_active = 1', [$this->id]);
    }

    /**
     * Obtiene departamentos para select
     */
    public static function forSelect(): array
    {
        $departments = self::where('is_active', 1)->get();
        $options = ['' => 'Sin departamento'];
        
        foreach ($departments as $dept) {
            $options[$dept->id] = $dept->name;
        }
        
        return $options;
    }
}
