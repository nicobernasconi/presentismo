<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo WorkCenter (Centro de Trabajo)
 */
class WorkCenter extends Model
{
    protected static string $table = 'work_centers';
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'tenant_id',
        'name',
        'code',
        'address',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'radius',
        'timezone',
        'requires_geolocation',
        'is_active',
    ];

    /**
     * Obtiene empleados del centro
     */
    public function employees(): array
    {
        return User::where('work_center_id', $this->id)
            ->where('is_active', 1)
            ->get();
    }

    /**
     * Cuenta empleados
     */
    public function employeeCount(): int
    {
        return User::count('work_center_id = ? AND is_active = 1', [$this->id]);
    }

    /**
     * Calcula la distancia en metros entre el centro y unas coordenadas dadas
     */
    public function calculateDistance(float $latitude, float $longitude): float
    {
        $earthRadius = 6371000; // Radio de la Tierra en metros
        
        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) ** 2 +
             cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c);
    }
    
    /**
     * Verifica si las coordenadas están dentro del radio permitido
     */
    public function isWithinRadius(float $latitude, float $longitude): bool
    {
        $distance = $this->calculateDistance($latitude, $longitude);
        return $distance <= ($this->radius ?? 100);
    }

    /**
     * Obtiene centros para select
     */
    public static function forSelect(): array
    {
        $centers = self::where('is_active', 1)->get();
        $options = ['' => 'Sin centro asignado'];
        
        foreach ($centers as $center) {
            $options[$center->id] = $center->name;
        }
        
        return $options;
    }
}
