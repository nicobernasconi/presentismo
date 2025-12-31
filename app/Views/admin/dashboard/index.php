<?php
/**
 * Dashboard Administrativo
 */
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Empresas Totales -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Empresas Totales</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistics['total_companies'] ?></p>
            </div>
            <i class="fas fa-building text-4xl text-blue-500 opacity-20"></i>
        </div>
    </div>

    <!-- Empresas Activas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Empresas Activas</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistics['active_companies'] ?></p>
            </div>
            <i class="fas fa-check-circle text-4xl text-green-500 opacity-20"></i>
        </div>
    </div>

    <!-- Total de Empleados -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total de Empleados</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistics['total_employees'] ?></p>
            </div>
            <i class="fas fa-users text-4xl text-purple-500 opacity-20"></i>
        </div>
    </div>

    <!-- Planes Disponibles -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Planes Disponibles</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?= $statistics['total_plans'] ?></p>
            </div>
            <i class="fas fa-layer-group text-4xl text-amber-500 opacity-20"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Empresas Recientes -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Empresas Recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_companies as $company): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                            <a href="<?= $baseUrl ?>/admin/empresas/<?= $company['id'] ?>" class="text-blue-600 hover:text-blue-900">
                                <?= htmlspecialchars($company['name'] ?? '') ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($company['email'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($company['plan_name'] ?? 'N/A') ?></td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($company['is_active']): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactiva</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="<?= $baseUrl ?>/admin/empresas" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                Ver todas las empresas →
            </a>
        </div>
    </div>

    <!-- Distribución por Plan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Distribución por Plan</h3>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($plan_distribution as $plan): ?>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($plan['name'] ?? 'Sin Plan') ?></span>
                    <span class="text-sm font-semibold text-gray-900"><?= $plan['count'] ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <?php 
                    $maxCount = max(array_column($plan_distribution, 'count')) ?: 1;
                    $percentage = ($plan['count'] / $maxCount) * 100;
                    ?>
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
