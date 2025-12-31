<?php
/**
 * Listado de Empresas
 */
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-900">Gestión de Empresas</h2>
    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas/crear" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-plus"></i> Nueva Empresa
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">RFC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Superadmin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($companies)): ?>
                    <?php foreach ($companies as $company): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium"><?= htmlspecialchars($company['name'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($company['tax_id'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($company['email'] ?? '') ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                <?= htmlspecialchars($company['plan_name'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php if (!empty($company['superadmin_name'])): ?>
                            <span class="text-gray-900 font-medium"><?= htmlspecialchars($company['superadmin_name']) ?></span><br>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($company['superadmin_email']) ?></span>
                            <?php else: ?>
                            <span class="text-red-600 text-xs font-medium"><i class="fas fa-exclamation-circle mr-1"></i>Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <?php if ($company['is_active']): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Activa</span>
                            <?php else: ?>
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas/<?= $company['id'] ?>" class="text-blue-600 hover:text-blue-900 text-xs font-medium">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas/<?= $company['id'] ?>/editar" class="text-amber-600 hover:text-amber-900 text-xs font-medium">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form method="POST" action="<?= htmlspecialchars($baseUrl ?? '') ?>/admin/empresas/<?= $company['id'] ?>/eliminar" style="display:inline;" 
                                  data-confirm="¿Está seguro?">
                                <input type="hidden" name="id" value="<?= $company['id'] ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-600">No hay empresas registradas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
