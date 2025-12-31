<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Core\Controller;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Mostrar lista de tareas
     */
    public function index()
    {
        $filters = [
            'status' => $this->input('status'),
            'project_id' => $this->input('project_id'),
            'assigned_to' => $this->input('assigned_to')
        ];

        $query = Task::query();

        // Aplicar filtros
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Si no es admin/supervisor, mostrar solo tareas asignadas al usuario
        $user = $_SESSION['user'] ?? null;
        if ($user && !in_array($user['role_name'], ['Super Admin', 'Admin', 'Supervisor'])) {
            $query->where('assigned_to', $user['id']);
        }

        $tasks = $query->all();

        // Obtener proyectos y usuarios para filtros
        $projects = Project::all();
        $users = User::all();

        return $this->view('tasks/index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
            'filters' => $filters
        ]);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->all();
        $users = User::where('is_active', true)->all();

        return $this->view('tasks/form', [
            'task' => null,
            'projects' => $projects,
            'users' => $users
        ]);
    }

    /**
     * Guardar nueva tarea
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min:3',
            'project_id' => 'required|numeric',
            'assigned_to' => 'required|numeric',
            'due_date' => 'required|date'
        ];

        $errors = $this->validate($this->input(), $rules);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $this->input();
            return $this->redirect('/tareas/crear');
        }

        $task = Task::create([
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'project_id' => $this->input('project_id'),
            'assigned_to' => $this->input('assigned_to'),
            'assigned_by' => $_SESSION['user']['id'],
            'priority' => $this->input('priority', 'medium'),
            'status' => 'pending',
            'due_date' => $this->input('due_date'),
            'estimated_hours' => $this->input('estimated_hours') ?: null
        ]);

        $_SESSION['success'] = 'Tarea creada exitosamente';
        return $this->redirect('/tareas');
    }

    /**
     * Mostrar detalles de una tarea
     */
    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            $_SESSION['error'] = 'Tarea no encontrada';
            return $this->redirect('/tareas');
        }

        $project = $task->project();
        $assignedUser = $task->assignedUser();
        $assignedByUser = $task->assignedByUser();

        return $this->view('tasks/show', [
            'task' => $task->toArray(),
            'project' => $project,
            'assignedUser' => $assignedUser,
            'assignedByUser' => $assignedByUser
        ]);
    }

    /**
     * Actualizar estado de tarea
     */
    public function updateStatus($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->json(['success' => false, 'message' => 'Tarea no encontrada'], 404);
        }

        $newStatus = $this->input('status');

        if (!in_array($newStatus, ['pending', 'in_progress', 'completed', 'cancelled'])) {
            return $this->json(['success' => false, 'message' => 'Estado inválido'], 400);
        }

        $task->status = $newStatus;

        if ($newStatus === 'completed') {
            $task->completed_at = date('Y-m-d H:i:s');
        }

        $task->save();

        $_SESSION['success'] = 'Estado de tarea actualizado';
        return $this->json(['success' => true, 'message' => 'Estado actualizado']);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $task = Task::find($id);

        if (!$task) {
            $_SESSION['error'] = 'Tarea no encontrada';
            return $this->redirect('/tareas');
        }

        $projects = Project::where('status', 'active')->all();
        $users = User::where('is_active', true)->all();

        return $this->view('tasks/form', [
            'task' => $task->toArray(),
            'projects' => $projects,
            'users' => $users
        ]);
    }

    /**
     * Actualizar tarea
     */
    public function update($id)
    {
        $task = Task::find($id);

        if (!$task) {
            $_SESSION['error'] = 'Tarea no encontrada';
            return $this->redirect('/tareas');
        }

        $rules = [
            'title' => 'required|min:3',
            'project_id' => 'required|numeric',
            'assigned_to' => 'required|numeric',
            'due_date' => 'required|date'
        ];

        $errors = $this->validate($this->input(), $rules);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $this->input();
            return $this->redirect("/tareas/{$id}/editar");
        }

        $task->title = $this->input('title');
        $task->description = $this->input('description');
        $task->project_id = $this->input('project_id');
        $task->assigned_to = $this->input('assigned_to');
        $task->priority = $this->input('priority');
        $task->status = $this->input('status');
        $task->due_date = $this->input('due_date');
        $task->estimated_hours = $this->input('estimated_hours') ?: null;
        $task->save();

        $_SESSION['success'] = 'Tarea actualizada exitosamente';
        return $this->redirect('/tareas');
    }

    /**
     * Eliminar tarea
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            $_SESSION['error'] = 'Tarea no encontrada';
            return $this->redirect('/tareas');
        }

        $task->delete();

        $_SESSION['success'] = 'Tarea eliminada exitosamente';
        return $this->redirect('/tareas');
    }
}
