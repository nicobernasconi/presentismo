<?php
/**
 * Vista: Detalle de Departamento
 * Variables:
 * - $department: array
 * - $employees: array
 * - $manager: array|null
 */

if (!is_array($department)) $department = [];
if (!is_array($employees)) $employees = [];
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Departamento: <?= htmlspecialchars($department['name'] ?? '') ?></h2>
            <p class="text-gray-600 mt-1">Código: <?= htmlspecialchars($department['code'] ?? '') ?></p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/departamentos" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Volver</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600">Empleados</p>
        <p class="text-2xl font-bold text-gray-900"><?= (int)count($employees) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600">Estado</p>
        <p class="text-2xl font-bold text-gray-900"><?= ((bool)($department['is_active'] ?? true)) ? 'Activo' : 'Inactivo' ?></p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <p class="text-sm text-gray-600">Responsable</p>
        <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($manager['name'] ?? 'Sin asignar') ?></p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-users text-primary-600 mr-2"></i>
            Empleados
        </h3>
        <?php if (empty($employees)): ?>
            <p class="text-gray-600">No hay empleados en este departamento</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($employees as $e): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?= htmlspecialchars($e->name ?? '') ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($e->email ?? '') ?></td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-green-800 bg-green-100 text-xs font-semibold">Activo</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
