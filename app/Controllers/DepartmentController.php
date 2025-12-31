<?php

namespace App\Controllers;

use App\Models\Department;
use Core\Controller;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Mostrar lista de departamentos
     */
    public function index()
    {
        $departments = Department::all();
        
        // Convertir a array y obtener estadísticas
        $deptArray = [];
        foreach ($departments as $dept) {
            $deptData = $dept->toArray();
            $deptData['employee_count'] = count($dept->employees());
            $deptArray[] = $deptData;
        }

        return $this->view('departments/index', [
            'departments' => $deptArray
        ]);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return $this->view('departments/form', [
            'department' => null
        ]);
    }

    /**
     * Guardar nuevo departamento
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min:3',
            'code' => 'required|min:2',
        ];

        $errors = $this->validate($this->input(), $rules);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $this->input();
            return $this->redirect('/departamentos/crear');
        }

        $department = Department::create([
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'description' => $this->input('description'),
            'manager_id' => $this->input('manager_id') ?: null,
            'is_active' => $this->input('is_active', true)
        ]);

        $_SESSION['success'] = 'Departamento creado exitosamente';
        return $this->redirect('/departamentos');
    }

    /**
     * Mostrar detalles de un departamento
     */
    public function show($id)
    {
        $department = Department::find($id);

        if (!$department) {
            $_SESSION['error'] = 'Departamento no encontrado';
            return $this->redirect('/departamentos');
        }

        $employees = $department->employees();
        $manager = $department->manager();

        return $this->view('departments/show', [
            'department' => $department->toArray(),
            'employees' => $employees,
            'manager' => $manager
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $department = Department::find($id);

        if (!$department) {
            $_SESSION['error'] = 'Departamento no encontrado';
            return $this->redirect('/departamentos');
        }

        return $this->view('departments/form', [
            'department' => $department->toArray()
        ]);
    }

    /**
     * Actualizar departamento
     */
    public function update($id)
    {
        $department = Department::find($id);

        if (!$department) {
            $_SESSION['error'] = 'Departamento no encontrado';
            return $this->redirect('/departamentos');
        }

        $rules = [
            'name' => 'required|min:3',
            'code' => 'required|min:2',
        ];

        $errors = $this->validate($this->input(), $rules);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $this->input();
            return $this->redirect("/departamentos/{$id}/editar");
        }

        $department->name = $this->input('name');
        $department->code = $this->input('code');
        $department->description = $this->input('description');
        $department->manager_id = $this->input('manager_id') ?: null;
        $department->is_active = $this->input('is_active', false);
        $department->save();

        $_SESSION['success'] = 'Departamento actualizado exitosamente';
        return $this->redirect('/departamentos');
    }

    /**
     * Eliminar departamento
     */
    public function destroy($id)
    {
        $department = Department::find($id);

        if (!$department) {
            $_SESSION['error'] = 'Departamento no encontrado';
            return $this->redirect('/departamentos');
        }

        // Verificar si tiene empleados asignados
        $employeeCount = count($department->employees());

        if ($employeeCount > 0) {
            $_SESSION['error'] = "No se puede eliminar el departamento porque tiene {$employeeCount} empleados asignados";
            return $this->redirect('/departamentos');
        }

        $department->delete();

        $_SESSION['success'] = 'Departamento eliminado exitosamente';
        return $this->redirect('/departamentos');
    }
}
