<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Role
 */
class Role extends Model
{
    protected static string $table = 'roles';
    protected static bool $useTenant = false;
    protected static bool $timestamps = false;
    
    protected static array $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'is_system',
    ];

    /**
     * Encuentra rol por slug
     */
    public static function findBySlug(string $slug): ?self
    {
        $sql = "SELECT * FROM roles WHERE slug = ? LIMIT 1";
        $data = self::db()->fetch($sql, [$slug]);
        
        return $data ? static::hydrate($data) : null;
    }

    /**
     * Obtiene los permisos del rol
     */
    public function permissions(): array
    {
        $sql = "SELECT p.* FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?";
        
        return self::db()->fetchAll($sql, [$this->id]);
    }

    /**
     * Verifica si tiene un permiso específico
     */
    public function hasPermission(string $slug): bool
    {
        $sql = "SELECT COUNT(*) as count FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ? AND p.slug = ?";
        
        $result = self::db()->fetch($sql, [$this->id, $slug]);
        
        return $result['count'] > 0;
    }

    /**
     * Obtiene todos los roles para select
     */
    public static function forSelect(): array
    {
        $roles = self::all();
        $options = [];
        
        foreach ($roles as $role) {
            $options[$role->id] = $role->name;
        }
        
        return $options;
    }
}
