<?php
/**
 * Vista: Detalle de Proyecto
 * Variables:
 * - $project: array
 * - $tasks: array
 * - $manager: array|null
 * - $stats: array
 */

if (!is_array($project)) $project = [];
if (!is_array($tasks)) $tasks = [];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Proyecto: <?= htmlspecialchars($project['name'] ?? '') ?></h2>
            <p class="text-gray-600 mt-1">Código: <?= htmlspecialchars($project['code'] ?? '') ?></p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/proyectos" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Volver</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600">Tareas Totales</p>
        <p class="text-2xl font-bold text-gray-900"><?= (int)($stats['total_tasks'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600">Horas Estimadas</p>
        <p class="text-2xl font-bold text-gray-900"><?= (int)($stats['total_hours'] ?? 0) ?> h</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600">Estado</p>
        <p class="text-2xl font-bold text-gray-900"><?= ucfirst($project['status'] ?? 'active') ?></p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-list text-primary-600 mr-2"></i>
            Tareas
        </h3>
        <?php if (empty($tasks)): ?>
            <p class="text-gray-600">No hay tareas</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($tasks as $t): ?>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($t['name'] ?? '') ?></p>
                                <p class="text-sm text-gray-600">Horas: <?= (int)($t['estimated_hours'] ?? 0) ?></p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800">
                                <?= htmlspecialchars(ucfirst($t['status'] ?? 'pending')) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
