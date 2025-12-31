<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\User;
use Core\Controller;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->setLayout('app');
    }

    /**
     * Mostrar lista de proyectos
     */
    public function index()
    {
        $filters = [
            'status' => $this->input('status'),
            'search' => $this->input('search')
        ];

        $query = Project::query();

        // Aplicar filtros
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'LIKE', "%{$filters['search']}%");
        }

        $projects = $query->all();

        // Obtener estadísticas de cada proyecto
        $projectArray = [];
        foreach ($projects as $project) {
            $projectData = $project->toArray();
            $tasks = $project->tasks();
            
            $projectData['total_tasks'] = count($tasks);
            $projectData['completed_tasks'] = count(array_filter($tasks, fn($t) => $t['status'] === 'completed'));
            $projectData['progress'] = $projectData['total_tasks'] > 0 
                ? round(($projectData['completed_tasks'] / $projectData['total_tasks']) * 100) 
                : 0;
            $projectArray[] = $projectData;
        }

        return $this->view('projects/index', [
            'projects' => $projectArray,
            'filters' => $filters
        ]);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $users = User::all();

        return $this->view('projects/form', [
            'project' => null,
            'users' => $users
        ]);
    }

    /**
     * Guardar nuevo proyecto
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min:3',
            'code' => 'required|min:2',
            'start_date' => 'required|date'
        ];

        $errors = $this->validate($this->input(), $rules);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $this->input();
            return $this->redirect('/proyectos/crear');
        }

        $project = Project::create([
            'name' => $this->input('name'),
            'code' => $this->input('code'),
            'description' => $this->input('description'),
            'start_date' => $this->input('start_date'),
            'end_date' => $this->input('end_date') ?: null,
            'status' => $this->input('status', 'active'),
            'manager_id' => $this->input('manager_id') ?: null,
            'budget' => $this->input('budget') ?: null
        ]);

        $_SESSION['success'] = 'Proyecto creado exitosamente';
        return $this->redirect('/proyectos');
    }

    /**
     * Mostrar detalles de un proyecto
     */
    public function show($id)
    {
        $project = Project::find($id);

        if (!$project) {
            $_SESSION['error'] = 'Proyecto no encontrado';
            return $this->redirect('/proyectos');
        }

        $tasks = $project->tasks();
        $manager = $project->manager();

        // Calcular estadísticas
        $stats = [
            'total_tasks' => count($tasks),
            'pending' => count(array_filter($tasks, fn($t) => $t['status'] === 'pending')),
            'in_progress' => count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')),
            'completed' => count(array_filter($tasks, fn($t) => $t['status'] === 'completed')),
            'total_hours' => array_sum(array_column($tasks, 'estimated_hours'))
        ];

        return $this->view('projects/show', [
            'project' => $project->toArray(),
            'tasks' => $tasks,
            'manager' => $manager,
            'stats' => $stats
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $project = Project::find($id);

        if (!$project) {
            $_SESSION['error'] = 'Proyecto no encontrado';
            return $this->redirect('/proyectos');
        }

        $users = User::all();

        return $this->view('projects/form', [
            'project' => $project->toArray(),
            'users' => $users
        ]);
    }

    /**
     * Actualizar proyecto
     */
    public function update($id)
    {
        $project = Project::find($id);

        if (!$project) {
            $_SESSION['error'] = 'Proyecto no encontrado';
            return $this->redirect('/proyectos');
        }

        $rules = [
            'name' => 'required|min:3',
            'code' => 'required|min:2',
            'start_date' => 'required|date'
        ];

        $errors = $this->validate($this->input(), $rules);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $this->input();
            return $this->redirect("/proyectos/{$id}/editar");
        }

        $project->name = $this->input('name');
        $project->code = $this->input('code');
        $project->description = $this->input('description');
        $project->start_date = $this->input('start_date');
        $project->end_date = $this->input('end_date') ?: null;
        $project->status = $this->input('status');
        $project->manager_id = $this->input('manager_id') ?: null;
        $project->budget = $this->input('budget') ?: null;
        $project->save();

        $_SESSION['success'] = 'Proyecto actualizado exitosamente';
        return $this->redirect('/proyectos');
    }

    /**
     * Eliminar proyecto
     */
    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            $_SESSION['error'] = 'Proyecto no encontrado';
            return $this->redirect('/proyectos');
        }

        // Verificar si tiene tareas asignadas
        $tasks = $project->tasks();

        if (count($tasks) > 0) {
            $_SESSION['error'] = "No se puede eliminar el proyecto porque tiene " . count($tasks) . " tareas asignadas";
            return $this->redirect('/proyectos');
        }

        $project->delete();

        $_SESSION['success'] = 'Proyecto eliminado exitosamente';
        return $this->redirect('/proyectos');
    }
}
