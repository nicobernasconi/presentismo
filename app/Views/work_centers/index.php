<?php
/**
 * Centros de Trabajo - Listado
 */
?>

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Centros de Trabajo</h2>
            <p class="text-gray-600 mt-1">Gestiona los centros de trabajo de la empresa</p>
        </div>
        <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/centros/crear" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Nuevo Centro
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <?php if (!empty($workCenters)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($workCenters as $center): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($center['name'] ?? '') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($center['code'] ?? '') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($center['city'] ?? '') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($center['is_active']): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/centros/<?= $center['id'] ?>/editar" class="text-green-600 hover:text-green-900"><i class="fas fa-edit"></i></a>
                            <form action="<?= htmlspecialchars($baseUrl ?? '') ?>/centros/<?= $center['id'] ?>/eliminar" method="POST" style="display:inline;" data-confirm="¿Eliminar este centro?">
                                <button type="submit" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-gray-600">No hay centros de trabajo</p>
        <?php endif; ?>
    </div>
</div>
