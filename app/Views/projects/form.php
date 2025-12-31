<?php
/**
 * Vista: Formulario de Proyecto (crear/editar)
 * Variables:
 * - $project: array|null
 * - $users: array
 */

if (!is_array($project)) $project = null;
if (!is_array($users)) $users = [];

$editing = !empty($project);
$action = $editing ? ($baseUrl ?? '') . '/proyectos/' . (int)$project['id'] : ($baseUrl ?? '') . '/proyectos';
$method = $editing ? 'POST' : 'POST';
$title = $editing ? 'Editar Proyecto' : 'Nuevo Proyecto';
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900"><?= $title ?></h2>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Volver</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="<?= $action ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
            <input type="text" name="name" value="<?= htmlspecialchars($project['name'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
            <input type="text" name="code" value="<?= htmlspecialchars($project['code'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
            <textarea name="description" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" rows="3"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Inicio</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($project['start_date'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fin</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($project['end_date'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <?php $status = $project['status'] ?? 'active'; ?>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="paused" <?= $status === 'paused' ? 'selected' : '' ?>>Pausado</option>
                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completado</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
            <select name="manager_id" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Sin asignar</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= (int)$u->id ?>" <?= isset($project['manager_id']) && $project['manager_id'] == $u->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u->name) ?> (<?= htmlspecialchars($u->email) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Presupuesto</label>
            <input type="number" step="0.01" name="budget" value="<?= htmlspecialchars($project['budget'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div class="md:col-span-2 flex justify-end gap-3 mt-2">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
                <?= $editing ? 'Guardar Cambios' : 'Crear Proyecto' ?>
            </button>
        </div>
    </form>
</div>
