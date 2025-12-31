<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\WorkCenter;
use App\Models\Shift;
use Core\Database;

/**
 * Controlador de Empleados
 */
class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Lista de empleados
     */
    public function index(): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para ver empleados');
            $this->redirect('/dashboard');
            return;
        }

        $db = Database::getInstance();
        $tenantId = Auth::tenantId();
        
        // Filtros
        $search = $this->query('search', '');
        $departmentId = $this->query('department_id', '');
        $status = $this->query('status', 'active');
        
        $sql = "SELECT u.*, d.name as department_name, r.name as role_name, wc.name as work_center_name
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN work_centers wc ON u.work_center_id = wc.id
                WHERE u.tenant_id = ? AND u.deleted_at IS NULL";
        
        $params = [$tenantId];
        
        if ($search) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.employee_code LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($departmentId) {
            $sql .= " AND u.department_id = ?";
            $params[] = $departmentId;
        }
        
        if ($status === 'active') {
            $sql .= " AND u.is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " AND u.is_active = 0";
        }
        
        $sql .= " ORDER BY u.name ASC";
        
        $employees = $db->fetchAll($sql, $params);
        $departments = Department::forSelect();

        $this->view('employees/index', [
            'title' => 'Empleados',
            'employees' => $employees,
            'departments' => $departments,
            'filters' => [
                'search' => $search,
                'department_id' => $departmentId,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Formulario de creación
     */
    public function create(): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para crear empleados');
            $this->redirect('/empleados');
            return;
        }

        $this->view('employees/form', [
            'title' => 'Nuevo Empleado',
            'employee' => null,
            'departments' => Department::forSelect(),
            'workCenters' => WorkCenter::forSelect(),
            'roles' => Role::forSelect(),
            'shifts' => Shift::forSelect(),
        ]);
    }

    /**
     * Guarda nuevo empleado
     */
    public function store(): void
    {
        if (!Auth::isSupervisor()) {
            $this->withError('No tienes permisos para crear empleados');
            $this->redirect('/empleados');
            return;
        }

        $data = $this->input();
        
        // Generar nombre completo si no existe
        if (empty($data['name']) && (!empty($data['first_name']) || !empty($data['last_name']))) {
            $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        }
        
        // Validar
        $errors = $this->validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role_id' => 'required|numeric',
        ]);
        
        // Validar que tenga nombre o first_name
        if (empty($data['name']) && empty($data['first_name'])) {
            $errors['name'] = ['El nombre es obligatorio'];
        }
        
        if (!empty($errors)) {
            $this->withErrors($errors)->withOld($data);
            $this->redirect('/empleados/crear');
            return;
        }

        // Verificar email único
        $existing = User::findByEmail($data['email']);
        if ($existing) {
            $this->withError('El email ya está registrado')
                 ->withOld($data);
            $this->redirect('/empleados/crear');
            return;
        }

        // Crear usuario
        $userData = [
            'tenant_id' => Auth::tenantId(),
            'role_id' => $data['role_id'],
            'email' => $data['email'],
            'password' => Auth::hashPassword($data['password']),
            'name' => $data['name'],
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'employee_code' => $data['employee_code'] ?? null,
            'dni' => $data['dni'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'department_id' => $data['department_id'] ?: null,
            'work_center_id' => $data['work_center_id'] ?: null,
            'position' => $data['position'] ?? null,
            'hire_date' => $data['hire_date'] ?: null,
            'contract_type' => $data['contract_type'] ?? null,
            'hours_per_week' => $data['hours_per_week'] ?? 40,
            'is_active' => 1,
        ];

        $user = User::create($userData);

        // Asignar turno si se especificó
        if (!empty($data['shift_id'])) {
            $db = Database::getInstance();
            $db->insert('shift_assignments', [
                'tenant_id' => Auth::tenantId(),
                'user_id' => $user->id,
                'shift_id' => $data['shift_id'],
                'start_date' => date('Y-m-d'),
                'is_active' => 1,
                'created_by' => Auth::id(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->withSuccess('Empleado creado correctamente');
        $this->redirect('/empleados');
    }

    /**
     * Ver empleado
     */
    public function show(int $id): void
    {        /** @var User|null $employee */        $employee = User::find($id);
        
        if (!$employee) {
            $this->withError('Empleado no encontrado');
            $this->redirect('/empleados');
            return;
        }

        $this->view('employees/show', [
            'title' => $employee->name,
            'employee' => $employee,
            'department' => Department::find($employee->department_id),
            'workCenter' => $employee->workCenter(),
            'role' => Role::find($employee->role_id),
            'currentShift' => $this->getCurrentShift($employee->id),
            'holidayBalance' => $employee->holidayBalance(),
        ]);
    }

    /**
     * Formulario de edición
     */
    public function edit(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->withError('No tienes permisos para editar empleados');
            $this->redirect('/empleados');
            return;
        }

        $employee = User::find($id);
        
        if (!$employee) {
            $this->withError('Empleado no encontrado');
            $this->redirect('/empleados');
            return;
        }

        // Obtener IDs de turnos asignados actualmente
        $db = Database::getInstance();
        $shiftIds = array_column(
            $db->fetchAll("SELECT shift_id FROM shift_assignments WHERE user_id = ? AND is_active = 1", [$id]),
            'shift_id'
        );

        $employeeData = $employee->toArray();
        $employeeData['shift_ids'] = $shiftIds;

        $this->view('employees/form', [
            'title' => 'Editar Empleado',
            'employee' => $employeeData,
            'departments' => Department::forSelect(),
            'workCenters' => WorkCenter::forSelect(),
            'roles' => Role::forSelect(),
            'shifts' => Shift::forSelect(),
        ]);
    }

    /**
     * Actualiza empleado
     */
    public function update(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->withError('No tienes permisos para editar empleados');
            $this->redirect('/empleados');
            return;
        }

        $employee = User::find($id);
        
        if (!$employee) {
            $this->withError('Empleado no encontrado');
            $this->redirect('/empleados');
            return;
        }

        $data = $this->input();
        
        // Generar nombre completo si no existe
        if (empty($data['name']) && (!empty($data['first_name']) || !empty($data['last_name']))) {
            $data['name'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        }
        
        // Usar email existente si no viene en el form
        if (empty($data['email'])) {
            $data['email'] = $employee->email;
        }
        
        // Validar
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'role_id' => 'required|numeric',
        ]);
        
        if (!empty($errors)) {
            $this->withErrors($errors)->withOld($data);
            $this->redirect("/empleados/{$id}/editar");
            return;
        }

        // Actualizar campos
        $employee->name = $data['name'];
        $employee->email = $data['email'];
        $employee->role_id = $data['role_id'];
        $employee->first_name = $data['first_name'] ?? null;
        $employee->last_name = $data['last_name'] ?? null;
        $employee->employee_code = $data['employee_code'] ?? null;
        $employee->dni = $data['dni'] ?? null;
        $employee->phone = $data['phone'] ?? null;
        $employee->mobile = $data['mobile'] ?? null;
        $employee->department_id = $data['department_id'] ?: null;
        $employee->work_center_id = $data['work_center_id'] ?: null;
        $employee->position = $data['position'] ?? null;
        $employee->hire_date = $data['hire_date'] ?: null;
        $employee->contract_type = $data['contract_type'] ?? null;
        $employee->hours_per_week = $data['hours_per_week'] ?? 40;
        $employee->is_active = isset($data['is_active']) ? 1 : 0;
        
        // Actualizar contraseña si se proporciona
        if (!empty($data['password'])) {
            $employee->password = Auth::hashPassword($data['password']);
        }
        
        $employee->save();

        // Actualizar turnos asignados
        $shifts = $data['shifts'] ?? [];
        
        $db = Database::getInstance();
        
        // Desactivar turnos actuales primero
        $db->query("UPDATE shift_assignments SET is_active = 0, updated_at = NOW() 
                    WHERE user_id = ? AND is_active = 1", [$id]);
        
        // Asignar nuevos turnos si hay alguno seleccionado
        if (!empty($shifts)) {
            foreach ($shifts as $shiftId) {
                $db->insert('shift_assignments', [
                    'tenant_id' => Auth::tenantId(),
                    'user_id' => $id,
                    'shift_id' => (int)$shiftId,
                    'start_date' => date('Y-m-d'),
                    'is_active' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->withSuccess('Empleado actualizado correctamente');
        $this->redirect('/empleados');
    }

    /**
     * Obtiene el turno actual del empleado
     */
    private function getCurrentShift(int $userId)
    {
        $db = Database::getInstance();
        $shift = $db->fetch("
            SELECT s.* FROM shifts s
            INNER JOIN shift_assignments sa ON s.id = sa.shift_id
            WHERE sa.user_id = ? AND sa.is_active = 1 AND sa.end_date IS NULL
            ORDER BY sa.created_at DESC LIMIT 1
        ", [$userId]);
        
        return $shift;
    }

    /**
     * Elimina empleado (soft delete)
     */
    public function destroy(int $id): void
    {
        if (!Auth::isAdmin()) {
            $this->withError('No tienes permisos para eliminar empleados');
            $this->redirect('/empleados');
            return;
        }

        $employee = User::find($id);
        
        if (!$employee) {
            $this->withError('Empleado no encontrado');
            $this->redirect('/empleados');
            return;
        }

        // No permitir eliminar el propio usuario
        if ($employee->id === Auth::id()) {
            $this->withError('No puedes eliminar tu propio usuario');
            $this->redirect('/empleados');
            return;
        }

        $employee->delete();

        $this->withSuccess('Empleado eliminado correctamente');
        $this->redirect('/empleados');
    }
}
