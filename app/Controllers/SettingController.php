<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $settings = $db->fetchAll(
            "SELECT * FROM settings WHERE tenant_id = ?",
            [$tenantId]
        );

        $this->view('settings.index', [
            'title' => 'Configuración',
            'settings' => $settings,
        ]);
    }

    public function update(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        // Actualizar configuraciones generales
        $settingsData = $this->input('settings', []);

        foreach ($settingsData as $key => $value) {
            $db->query(
                "INSERT INTO settings (tenant_id, `key`, `value`) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)",
                [$tenantId, $key, $value]
            );
        }

        $this->withSuccess('Configuración actualizada exitosamente');
        $this->redirect('/configuracion');
    }

    public function company(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $tenant = $db->fetch(
            "SELECT * FROM tenants WHERE id = ?",
            [$tenantId]
        );

        $this->view('settings.company', [
            'title' => 'Configuración de Empresa',
            'tenant' => $tenant,
        ]);
    }

    public function updateCompany(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $data = [
            'name' => $this->input('name'),
            'slug' => $this->input('slug'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'postal_code' => $this->input('postal_code'),
            'country' => $this->input('country'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->update('tenants', $data, "id = ?", [$tenantId]);
        $this->withSuccess('Información de empresa actualizada exitosamente');
        $this->redirect('/configuracion/empresa');
    }
}
