<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\WorkCenter;
use Core\Database;

class WorkCenterController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    public function index(): void
    {
        if (!Auth::isAdmin()) {
            $this->withError('No tienes permisos para ver centros de trabajo');
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $workCenters = $db->fetchAll(
            "SELECT * FROM work_centers WHERE tenant_id = ? AND deleted_at IS NULL ORDER BY name ASC",
            [$tenantId]
        );

        $this->view('work_centers.index', [
            'title' => 'Centros de Trabajo',
            'workCenters' => $workCenters,
        ]);
    }

    public function create(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('work_centers.form', [
            'title' => 'Nuevo Centro de Trabajo',
            'workCenter' => null,
        ]);
    }

    public function store(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $data = [
            'tenant_id' => $tenantId,
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'postal_code' => $this->input('postal_code'),
            'is_active' => $this->input('is_active', 1),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $db->insert('work_centers', $data);
        $this->withSuccess('Centro de trabajo creado exitosamente');
        $this->redirect('/centros');
    }

    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $workCenter = $db->fetch(
            "SELECT * FROM work_centers WHERE id = ? AND tenant_id = ?",
            [$id, $tenantId]
        );

        if (!$workCenter) {
            $this->withError('Centro de trabajo no encontrado');
            $this->redirect('/centros');
            return;
        }

        $this->view('work_centers.form', [
            'title' => 'Editar Centro de Trabajo',
            'workCenter' => $workCenter,
        ]);
    }

    public function update(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $data = [
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'postal_code' => $this->input('postal_code'),
            'is_active' => $this->input('is_active', 1),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->update('work_centers', $data, "id = ? AND tenant_id = ?", [$id, $tenantId]);
        $this->withSuccess('Centro de trabajo actualizado exitosamente');
        $this->redirect('/centros');
    }

    public function destroy(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/dashboard');
            return;
        }

        $tenantId = Auth::tenantId();
        $db = Database::getInstance();

        $db->update('work_centers', ['deleted_at' => date('Y-m-d H:i:s')], "id = ? AND tenant_id = ?", [$id, $tenantId]);
        $this->withSuccess('Centro de trabajo eliminado exitosamente');
        $this->redirect('/centros');
    }
}
