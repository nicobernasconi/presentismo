<?php
/**
 * Detalles de Empresa
 */
?>

<div class="mb-6">
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Empresas
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Información de la Empresa -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($company['name'] ?? '') ?></h2>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas/<?= $company['id'] ?>/editar" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>

        <div class="space-y-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">RFC</p>
                    <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($company['tax_id'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($company['email'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Teléfono</p>
                    <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($company['phone'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Plan</p>
                    <p class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($company['plan_name'] ?? 'N/A') ?></p>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-600">Dirección</p>
                <p class="text-gray-900"><?= htmlspecialchars($company['address'] ?? 'N/A') ?></p>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <p class="text-sm text-gray-600">Creada</p>
                    <p class="text-gray-900"><?= date('d/m/Y H:i', strtotime($company['created_at'])) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estado</p>
                    <p class="text-gray-900">
                        <?php if ($company['is_active']): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Activa</span>
                        <?php else: ?>
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">Inactiva</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 h-fit">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>
        
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Límite de Empleados</p>
                <p class="text-3xl font-bold text-blue-600"><?= $company['max_employees'] ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Empleados Actuales</p>
                <p class="text-3xl font-bold text-purple-600"><?= count($employees) ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Capacidad Usada</p>
                <?php $percentage = $company['max_employees'] > 0 ? (count($employees) / $company['max_employees']) * 100 : 0; ?>
                <p class="text-3xl font-bold text-gray-900"><?= round($percentage) ?>%</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Empleados de la Empresa -->
<div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Empleados (<?= count($employees) ?>)</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $employee): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?= htmlspecialchars($employee['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($employee['email']) ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($employee['is_active']): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Activo</span>
                            <?php else: ?>
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">Inactivo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-600">No hay empleados registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
