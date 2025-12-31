<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo AbsenceType (Tipo de Ausencia)
 */
class AbsenceType extends Model
{
    protected static string $table = 'absence_types';
    protected static bool $useTenant = false; // Incluye tipos globales (tenant_id = NULL)
    
    protected static array $fillable = [
        'tenant_id',
        'name',
        'code',
        'color',
        'icon',
        'is_paid',
        'requires_approval',
        'requires_document',
        'max_days_per_year',
        'advance_notice_days',
        'is_active',
    ];

    /**
     * Obtiene tipos disponibles para un tenant
     */
    public static function forTenant(int $tenantId): array
    {
        $sql = "SELECT * FROM absence_types 
                WHERE (tenant_id = ? OR tenant_id IS NULL) 
                AND is_active = 1 
                ORDER BY name ASC";
        
        $results = self::db()->fetchAll($sql, [$tenantId]);
        
        return array_map(fn($data) => static::hydrate($data), $results);
    }

    /**
     * Para select
     */
    public static function forSelect(int $tenantId): array
    {
        $types = self::forTenant($tenantId);
        $options = [];
        
        foreach ($types as $type) {
            $options[$type->id] = $type->name;
        }
        
        return $options;
    }

    /**
     * Etiqueta de remunerado
     */
    public function getPaidLabel(): string
    {
        return $this->is_paid ? 'Remunerada' : 'No remunerada';
    }
}
