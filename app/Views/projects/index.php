<?php
/**
 * Vista: Lista de Proyectos
 * Variables:
 * - $projects: array de proyectos con stats
 * - $filters: filtros aplicados
 */

if (!is_array($projects)) $projects = [];
if (!is_array($filters)) $filters = [];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Proyectos</h2>
            <p class="text-gray-600 mt-1">Gestión de proyectos y tareas</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos/crear" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Proyecto
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
    <form method="GET" action="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Todos</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Activo</option>
                <option value="paused" <?= ($filters['status'] ?? '') === 'paused' ? 'selected' : '' ?>>Pausado</option>
                <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completado</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Nombre o código">
        </div>
        <div class="flex items-end gap-2">
            <button class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">Buscar</button>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg"><i class="fas fa-times"></i></a>
        </div>
    </form>
</div>

<?php if (empty($projects)): ?>
<div class="text-center py-12 bg-white rounded-lg border border-gray-200">
    <i class="fas fa-project-diagram text-5xl text-gray-300 mb-4"></i>
    <p class="text-gray-600">No hay proyectos</p>
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos/crear" class="inline-block mt-4 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg"><i class="fas fa-plus mr-2"></i>Nuevo Proyecto</a>
</div>
<?php else: ?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progreso</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($projects as $p): ?>
            <tr>
                <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?= htmlspecialchars($p['name'] ?? '') ?></td>
                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($p['code'] ?? '') ?></td>
                <td class="px-6 py-4 text-sm">
                    <?php $status = $p['status'] ?? 'active'; $color = $status === 'completed' ? 'green' : ($status === 'paused' ? 'yellow' : 'blue'); ?>
                    <span class="px-2 py-1 rounded-full text-<?= $color ?>-800 bg-<?= $color ?>-100 text-xs font-semibold">
                        <?= ucfirst($status) ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="w-40">
                        <div class="h-2 bg-gray-200 rounded">
                            <div class="h-2 bg-primary-600 rounded" style="width: <?= (int)($p['progress'] ?? 0) ?>%"></div>
                        </div>
                        <span class="text-xs text-gray-600"><?= (int)($p['progress'] ?? 0) ?>%</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos/<?= (int)($p['id'] ?? 0) ?>" class="text-primary-600 hover:text-primary-800 mr-3"><i class="fas fa-eye"></i></a>
                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos/<?= (int)($p['id'] ?? 0) ?>/editar" class="text-gray-600 hover:text-gray-900"><i class="fas fa-edit"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
