<?php
namespace App\Models;

use Core\Model;

/**
 * Modelo Tenant (Empresa)
 */
class Tenant extends Model
{
    protected static string $table = 'tenants';
    protected static bool $useTenant = false; // Los tenants no tienen tenant_id
    protected static bool $softDeletes = true;
    
    protected static array $fillable = [
        'name',
        'slug',
        'tax_id',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'logo_path',
        'settings',
        'subscription_plan',
        'subscription_ends_at',
        'is_active',
    ];

    /**
     * Obtiene todos los usuarios del tenant
     */
    public function users(): array
    {
        return User::where('tenant_id', $this->id)->get();
    }

    /**
     * Obtiene solo los superadmins del tenant (role_id = 1)
     */
    public function superAdmins(): array
    {
        return User::where('tenant_id', $this->id)
            ->where('role_id', 1)
            ->get();
    }

    /**
     * Obtiene solo los empleados activos del tenant
     */
    public function activeEmployees(): array
    {
        return User::where('tenant_id', $this->id)
            ->where('is_active', 1)
            ->get();
    }

    /**
     * Obtiene todos los departamentos
     */
    public function departments(): array
    {
        return Department::where('tenant_id', $this->id)->get();
    }

    /**
     * Obtiene todos los centros de trabajo
     */
    public function workCenters(): array
    {
        return WorkCenter::where('tenant_id', $this->id)->get();
    }

    /**
     * Obtiene configuración específica
     */
    public function getSetting(string $key, $default = null)
    {
        $settings = json_decode($this->settings ?? '{}', true);
        return $settings[$key] ?? $default;
    }

    /**
     * Establece configuración
     */
    public function setSetting(string $key, $value): void
    {
        $settings = json_decode($this->settings ?? '{}', true);
        $settings[$key] = $value;
        $this->settings = json_encode($settings);
    }
}
